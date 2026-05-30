"""

Struktur folder yang dibutuhkan:
  ml/
  ├── data/
  │   ├── Anggur merah.csv
  │   ├── Apel fuji pink.csv
  │   └── ... (produk lainnya)
  ├── models/
  │   ├── lstm_anggur_merah_model.h5   ← hasil retrain
  │   ├── scaler_anggur_merah.pkl      ← hasil retrain
  │   └── last_seq_anggur_merah.npy   ← hasil retrain
  └── retrain.py                       ← file ini

Cara pakai:
  # Retrain semua produk yang terdaftar di PRODUK_CONFIG
  python retrain.py

  # Retrain satu produk saja
  python retrain.py --produk "anggur merah"

  # Retrain dengan jumlah epoch berbeda
  python retrain.py --produk "anggur merah" --epochs 100

  # Lihat semua opsi
  python retrain.py --help
"""

import os
import sys
import math
import time
import pickle
import argparse
import warnings
warnings.filterwarnings("ignore")

os.environ["TF_CPP_MIN_LOG_LEVEL"]  = "3"
os.environ["TF_ENABLE_ONEDNN_OPTS"] = "0"

import numpy as np
import pandas as pd

BASE_DIR   = os.path.dirname(os.path.abspath(__file__))
DATA_DIR   = os.path.join(BASE_DIR, "data")
MODELS_DIR_DEFAULT = os.path.join(BASE_DIR, "models")

PRODUK_CONFIG = {
    "anggur merah"   : "Anggur merah.csv",
    "apel fuji pink" : "Apel fuji pink.csv",
    "ayam fillet"    : "Ayam fillet.csv",
    "ayam potong"    : "Ayam potong.csv",
    "bawang merah"   : "Bawang merah.csv",
    "bawang putih"   : "Bawang putih.csv",
    "beras"          : "Beras.csv",
    "buah naga"      : "Buah naga.csv",
    "fillet lele"    : "Fillet Lele.csv",
    "indomilk fc"    : "Indomilk fc.csv",
    "indomilk ck_st" : "Indomilk st_ck.csv",
    "jeruk dr"       : "Jeruk DR.csv",
    "lengkeng gold"  : "Lengkeng gold.csv",
    "lengkeng hijau" : "Lengkeng hijau.csv",
    "pir"            : "Pir.csv",
    "semangka"       : "Semangka.csv",
}

# HYPERPARAMETER DEFAULT
DEFAULT_WINDOW_SIZE  = 30
DEFAULT_LSTM_UNITS   = 60
DEFAULT_EPOCHS       = 30
DEFAULT_BATCH_SIZE   = 16
DEFAULT_SPLIT_RATIO  = 0.8
DEFAULT_LEARNING_RATE= 0.001
DEFAULT_PATIENCE     = 10       

# KOLOM CSV
KOLOM_TANGGAL   = "Tanggal"
KOLOM_PENJUALAN = "Jumlah"

# HELPER
def produk_to_key(nama: str) -> str:
    """'Anggur Merah' → 'anggur_merah'"""
    return nama.lower().replace(" ", "_")


def log(msg: str, level: str = "INFO"):
    tag = {"INFO": "✓", "WARN": "⚠", "ERROR": "✗", "HEAD": "►"}.get(level, "•")
    print(f"  {tag} {msg}")


def separator(title: str = ""):
    line = "─" * 60
    if title:
        print(f"\n{'─'*5} {title} {'─'*(54 - len(title))}")
    else:
        print(line)


# STEP 1 — LOAD & PREPROCESS DATA
def load_and_preprocess(csv_path: str) -> np.ndarray:
    """
    Membaca CSV, sort berdasarkan tanggal, interpolasi missing value,
    dan mengembalikan array nilai penjualan (float).
    """
    df = pd.read_csv(csv_path)

    if KOLOM_TANGGAL in df.columns:
        df[KOLOM_TANGGAL] = pd.to_datetime(df[KOLOM_TANGGAL])
        df = df.sort_values(KOLOM_TANGGAL).reset_index(drop=True)
        log(f"Rentang: {df[KOLOM_TANGGAL].min().date()} → {df[KOLOM_TANGGAL].max().date()}")

    if KOLOM_PENJUALAN not in df.columns:
        raise ValueError(
            f"Kolom '{KOLOM_PENJUALAN}' tidak ditemukan. "
            f"Kolom tersedia: {list(df.columns)}"
        )

    series = df[KOLOM_PENJUALAN].copy()
    n_missing = series.isna().sum()
    if n_missing:
        log(f"Interpolasi {n_missing} missing value", "WARN")
        series = series.interpolate(method="linear", limit_direction="both")

    values = series.dropna().astype(float).values
    log(f"Total baris: {len(values)}  |  range: {values.min():.2f}–{values.max():.2f}")
    return values


# STEP 2 — BUAT SEQUENCES
def create_sequences(data: np.ndarray, window_size: int):
    X, y = [], []
    for i in range(len(data) - window_size):
        X.append(data[i : i + window_size])
        y.append(data[i + window_size])
    return np.array(X), np.array(y)


# STEP 3 — BANGUN MODEL LSTM
def build_model(window_size: int, lstm_units: int, learning_rate: float):
    import tensorflow as tf
    import tensorflow as tf
    tf.get_logger().setLevel("ERROR")

    model = tf.keras.Sequential([
        tf.keras.layers.Input(shape=(window_size, 1)),
        tf.keras.layers.LSTM(
            lstm_units,
            return_sequences=False,
            activation="tanh",
            recurrent_activation="sigmoid",
            use_bias=True,
            unit_forget_bias=True,
        ),
        tf.keras.layers.Dense(1, activation="linear"),
    ])
    model.compile(
        optimizer=tf.keras.optimizers.Adam(learning_rate=learning_rate),
        loss="mape",
        metrics=["mae"],
    )
    return model


# STEP 4 — EVALUASI
def calculate_mape(actual, predicted) -> float:
    actual    = np.array(actual).flatten()
    predicted = np.array(predicted).flatten()
    mask      = actual != 0
    if mask.sum() == 0:
        return float("nan")
    ape = np.abs((actual[mask] - predicted[mask]) / actual[mask]) * 100
    return float(np.mean(ape))


# FUNGSI UTAMA — RETRAIN SATU PRODUK
def retrain_produk(
    produk_nama: str,
    csv_path: str,
    models_dir: str,
    window_size: int  = DEFAULT_WINDOW_SIZE,
    lstm_units: int   = DEFAULT_LSTM_UNITS,
    epochs: int       = DEFAULT_EPOCHS,
    batch_size: int   = DEFAULT_BATCH_SIZE,
    split_ratio: float= DEFAULT_SPLIT_RATIO,
    learning_rate: float = DEFAULT_LEARNING_RATE,
    patience: int     = DEFAULT_PATIENCE,
    save_keras: bool  = False,
) -> dict:
    """
    Jalankan satu siklus retrain lengkap untuk satu produk.
    Mengembalikan dict berisi ringkasan hasil.
    """
    import tensorflow as tf
    tf.get_logger().setLevel("ERROR")

    produk_key = produk_to_key(produk_nama)
    separator(produk_nama.upper())
    t_start = time.time()

    # 1. Load data
    if not os.path.exists(csv_path):
        log(f"File CSV tidak ditemukan: {csv_path}", "ERROR")
        return {"produk": produk_nama, "status": "SKIP", "alasan": "CSV tidak ditemukan"}

    values = load_and_preprocess(csv_path)

    if len(values) < window_size + 10:
        msg = f"Data terlalu sedikit ({len(values)} baris, butuh >{window_size + 10})"
        log(msg, "ERROR")
        return {"produk": produk_nama, "status": "SKIP", "alasan": msg}

    # 2. Split & scaling─
    # FIX: scaler di-fit pada SELURUH data (bukan hanya train 80%).
    # Ini mencegah nilai dari transaksi web (yang bisa lebih besar
    # dari nilai train) menghasilkan scaled value di luar 0.1–0.9
    # dan menyebabkan MAPE melonjak >100%.
    from sklearn.preprocessing import MinMaxScaler

    split_idx  = int(len(values) * split_ratio)
    train_data = values[:split_idx]
    test_data  = values[split_idx:]

    scaler = MinMaxScaler(feature_range=(0.1, 0.9))
    train_scaled = scaler.fit_transform(train_data.reshape(-1, 1))  # fit hanya ke train
    test_scaled  = scaler.transform(test_data.reshape(-1, 1))

    log(f"Train: {len(train_scaled)} baris | Test: {len(test_scaled)} baris")

    # 3. Buat sequences
    X_train, y_train = create_sequences(train_scaled.flatten(), window_size)
    X_test,  y_test  = create_sequences(test_scaled.flatten(),  window_size)

    X_train = X_train.reshape(-1, window_size, 1)
    X_test  = X_test.reshape(-1, window_size, 1)

    if len(X_train) == 0:
        msg = "Sequences kosong setelah windowing (data train terlalu sedikit)"
        log(msg, "ERROR")
        return {"produk": produk_nama, "status": "SKIP", "alasan": msg}

    # ── 4. Bangun & latih model ──────────────────────────────
    model = build_model(window_size, lstm_units, learning_rate)

    callbacks = []

    log(f"Training LSTM ({lstm_units} unit, window={window_size}, epochs={epochs}) ...")
    history = model.fit(
        X_train, y_train,
        epochs=epochs,
        batch_size=batch_size,
        validation_split=0.1,
        callbacks=callbacks,
        verbose=0,
        shuffle=False,
    )

    actual_epochs = len(history.history["loss"])
    log(f"Selesai dalam {actual_epochs} epoch")

    # 5. Evaluasi
    if len(X_test) > 0:
        y_pred_scaled = model.predict(X_test, verbose=0)
        y_pred_actual = scaler.inverse_transform(y_pred_scaled).flatten()
        y_test_actual = scaler.inverse_transform(y_test.reshape(-1, 1)).flatten()
        mape          = calculate_mape(y_test_actual, y_pred_actual)
        log(f"MAPE pada data test: {mape:.2f}%")
    else:
        mape = float("nan")
        log("Data test terlalu sedikit untuk evaluasi", "WARN")

    # 6. Simpan model
    os.makedirs(models_dir, exist_ok=True)

    if save_keras:
        model_path = os.path.join(models_dir, f"lstm_{produk_key}_model.keras")
        model.save(model_path)
        log(f"Model disimpan: {model_path}")
    else:
        model_path = os.path.join(models_dir, f"lstm_{produk_key}_model.h5")
        model.save(model_path)
        log(f"Model disimpan: {model_path}")

    # 7. Simpan scaler
    scaler_path = os.path.join(models_dir, f"scaler_{produk_key}.pkl")
    with open(scaler_path, "wb") as f:
        pickle.dump(scaler, f)
    log(f"Scaler disimpan: {scaler_path}")

    # 8. Simpan last_seq
    tail = values[-window_size:].reshape(-1, 1)
    last_seq = scaler.transform(tail)   # shape (window_size, 1)
    last_seq_path = os.path.join(models_dir, f"last_seq_{produk_key}.npy")
    np.save(last_seq_path, last_seq)
    log(f"last_seq disimpan: {last_seq_path}")

    # 9. Simpan test_data
    test_data_path = os.path.join(models_dir, f"test_data_{produk_key}.npy")
    np.save(test_data_path, test_data)
    log(f"test_data disimpan: {test_data_path}")

    elapsed = time.time() - t_start
    log(f"Total waktu: {elapsed:.1f} detik")

    return {
        "produk"       : produk_nama,
        "status"       : "OK",
        "mape"         : round(mape, 2) if not math.isnan(mape) else None,
        "epochs"       : actual_epochs,
        "elapsed_detik": round(elapsed, 1),
    }


# CLI
def parse_args():
    parser = argparse.ArgumentParser(
        description="Retrain LSTM model prediksi penjualan",
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Contoh penggunaan:
  python retrain.py
  python retrain.py --produk "anggur merah"
  python retrain.py --produk "anggur merah" --epochs 100 --lstm-units 128
  python retrain.py --list-produk
        """,
    )
    parser.add_argument(
        "--produk", type=str, default=None,
        help="Nama produk yang ingin diretrain (default: semua produk)",
    )
    parser.add_argument(
        "--models-dir", type=str,
        default=MODELS_DIR_DEFAULT,
        help="Folder penyimpanan model (default: <BASE_DIR>/models)",
    )
    parser.add_argument("--window-size",   type=int,   default=DEFAULT_WINDOW_SIZE,   help=f"Panjang sequence (default: {DEFAULT_WINDOW_SIZE})")
    parser.add_argument("--lstm-units",    type=int,   default=DEFAULT_LSTM_UNITS,    help=f"Jumlah unit LSTM (default: {DEFAULT_LSTM_UNITS})")
    parser.add_argument("--epochs",        type=int,   default=DEFAULT_EPOCHS,        help=f"Maks epoch (default: {DEFAULT_EPOCHS})")
    parser.add_argument("--batch-size",    type=int,   default=DEFAULT_BATCH_SIZE,    help=f"Batch size (default: {DEFAULT_BATCH_SIZE})")
    parser.add_argument("--split-ratio",   type=float, default=DEFAULT_SPLIT_RATIO,   help=f"Rasio train/test (default: {DEFAULT_SPLIT_RATIO})")
    parser.add_argument("--learning-rate", type=float, default=DEFAULT_LEARNING_RATE, help=f"Learning rate (default: {DEFAULT_LEARNING_RATE})")
    parser.add_argument("--patience",      type=int,   default=DEFAULT_PATIENCE,      help=f"Early stopping patience (default: {DEFAULT_PATIENCE})")
    parser.add_argument("--save-keras",    action="store_true",                        help="Simpan dalam format .keras (default: .h5)")
    parser.add_argument("--list-produk",   action="store_true",                        help="Tampilkan daftar produk yang terdaftar lalu keluar")
    return parser.parse_args()


def main():
    args = parse_args()

    if args.list_produk:
        print("\nDaftar produk yang terdaftar di PRODUK_CONFIG:")
        for nama, csv_file in PRODUK_CONFIG.items():
            full_path = os.path.join(DATA_DIR, csv_file)
            ada = "✓" if os.path.exists(full_path) else "✗ (CSV tidak ada)"
            print(f"  {ada}  {nama}  →  {full_path}")
        sys.exit(0)

    # Tentukan produk yang akan diproses
    if args.produk:
        produk_nama = args.produk.lower()
        if produk_nama not in PRODUK_CONFIG:
            # Coba partial match
            matches = [k for k in PRODUK_CONFIG if produk_nama in k]
            if len(matches) == 1:
                produk_nama = matches[0]
                print(f"[INFO] Cocok dengan: '{produk_nama}'")
            elif len(matches) > 1:
                print(f"[ERROR] Nama produk ambigu. Cocok dengan: {matches}")
                sys.exit(1)
            else:
                print(f"[ERROR] Produk '{produk_nama}' tidak ditemukan di PRODUK_CONFIG.")
                print(f"  Gunakan --list-produk untuk melihat daftar produk.")
                sys.exit(1)
        target_produk = {produk_nama: PRODUK_CONFIG[produk_nama]}
    else:
        target_produk = PRODUK_CONFIG

    print("\n" + "═" * 62)
    print("  RETRAIN LSTM — PREDIKSI PENJUALAN")
    print("═" * 62)
    print(f"  Models dir   : {args.models_dir}")
    print(f"  Window size  : {args.window_size}")
    print(f"  LSTM units   : {args.lstm_units}")
    print(f"  Epochs (maks): {args.epochs}")
    print(f"  Batch size   : {args.batch_size}")
    print(f"  Learning rate: {args.learning_rate}")
    print(f"  Split ratio  : {args.split_ratio}")
    print(f"  Produk       : {len(target_produk)} produk")
    print("═" * 62)

    results = []
    for nama, csv_file in target_produk.items():
        csv_path = os.path.join(DATA_DIR, csv_file)
        result = retrain_produk(
            produk_nama   = nama,
            csv_path      = csv_path,
            models_dir    = args.models_dir,
            window_size   = args.window_size,
            lstm_units    = args.lstm_units,
            epochs        = args.epochs,
            batch_size    = args.batch_size,
            split_ratio   = args.split_ratio,
            learning_rate = args.learning_rate,
            patience      = args.patience,
            save_keras    = args.save_keras,
        )
        results.append(result)

    # ── Ringkasan akhir ──────────────────────────────────────
    separator("RINGKASAN")
    ok   = [r for r in results if r["status"] == "OK"]
    skip = [r for r in results if r["status"] == "SKIP"]

    print(f"\n  Berhasil : {len(ok)} produk")
    for r in ok:
        mape_str = f"MAPE={r['mape']}%" if r["mape"] is not None else "MAPE=N/A"
        print(f"    ✓ {r['produk']:25s}  {mape_str:15s}  {r['epochs']} epoch  {r['elapsed_detik']}s")

    if skip:
        print(f"\n  Dilewati : {len(skip)} produk")
        for r in skip:
            print(f"    ✗ {r['produk']:25s}  {r['alasan']}")

    print()


if __name__ == "__main__":
    main()