import os
import pickle
import numpy as np
import pandas as pd

# ── Konfigurasi ───────────────────────────────────────────────────────────────
PRODUK_CSV = {
    #'anggur merah': 'ml/data/Anggur merah.csv',
    #'apel fuji pink': 'ml/data/Apel fuji pink.csv',
    #'ayam fillet': 'ml/data/Ayam fillet.csv',
    #'ayam potong': 'ml/data/Ayam potong.csv',
    #'bawang merah': 'ml/data/Bawang merah.csv',
    #'bawang putih': 'ml/data/Bawang putih.csv',
    #'beras': 'ml/data/Beras.csv',
    'buah naga': 'ml/data/Buah naga.csv',
    #'fillet lele': 'ml/data/Fillet Lele.csv',
    #'indomilk fc': 'ml/data/Indomilk fc.csv',
    #'indomilk ck_st': 'ml/data/Indomilk ck_st.csv',
    #'Jeruk DR': 'ml/data/Jeruk DR.csv',
    #'lengkeng gold': 'ml/data/Lengkeng gold.csv',
    #'lengkeng hijau': 'ml/data/Lengkeng hijau.csv',
    #'pir': 'ml/data/Pir.csv',
    #'semangka': 'ml/data/Semangka.csv',
}

KOLOM_PENJUALAN = 'Jumlah'
KOLOM_TANGGAL   = 'Tanggal'

MODELS_DIR  = os.path.join(os.path.dirname(__file__), 'models')
WINDOW_SIZE = 30

# ── Proses ────────────────────────────────────────────────────────────────────

for produk_key, csv_path in PRODUK_CSV.items():
    print(f'\n[{produk_key}]')

    # 1. Baca CSV
    if not os.path.exists(csv_path):
        print(f'  SKIP — file CSV tidak ditemukan: {csv_path}')
        continue

    df = pd.read_csv(csv_path)

    # 2. Sort berdasarkan tanggal jika kolom tersedia
    if KOLOM_TANGGAL and KOLOM_TANGGAL in df.columns:
        df[KOLOM_TANGGAL] = pd.to_datetime(df[KOLOM_TANGGAL])
        df = df.sort_values(KOLOM_TANGGAL).reset_index(drop=True)
        print(f'  Rentang data: {df[KOLOM_TANGGAL].min().date()} → {df[KOLOM_TANGGAL].max().date()}')

    # 3. Ambil kolom penjualan
    if KOLOM_PENJUALAN not in df.columns:
        print(f'  ERROR — kolom "{KOLOM_PENJUALAN}" tidak ada. Kolom tersedia: {list(df.columns)}')
        continue

    series = df[KOLOM_PENJUALAN].dropna().astype(float).values
    print(f'  Total baris data: {len(series)}')

    if len(series) < WINDOW_SIZE:
        print(f'  ERROR — butuh minimal {WINDOW_SIZE} baris, hanya ada {len(series)}')
        continue

    # 4. Ambil 30 data terakhir
    tail = series[-WINDOW_SIZE:]

    # 5. Load scaler
    scaler_path = os.path.join(MODELS_DIR, f'scaler_{produk_key}.pkl')
    if not os.path.exists(scaler_path):
        print(f'  ERROR — scaler tidak ditemukan: {scaler_path}')
        continue

    with open(scaler_path, 'rb') as f:
        scaler = pickle.load(f)

    # 6. Normalisasi & simpan
    normalized    = scaler.transform(tail.reshape(-1, 1))   # shape (30, 1)
    last_seq_path = os.path.join(MODELS_DIR, f'last_seq_{produk_key}.npy')
    np.save(last_seq_path, normalized)

    print(f'  OK — disimpan ke: {last_seq_path}')
    print(f'  30 nilai terakhir (asli): {tail.tolist()}')

print('\nSelesai.')
