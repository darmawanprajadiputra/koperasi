import os
os.environ['TF_CPP_MIN_LOG_LEVEL']  = '3'
os.environ['TF_ENABLE_ONEDNN_OPTS'] = '0'

import sys
import json
import warnings
warnings.filterwarnings('ignore')

import numpy as np
import pickle
import math

import tensorflow as tf
tf.get_logger().setLevel('ERROR')

MODELS_DIR  = os.path.join(os.path.dirname(__file__), 'models')
WINDOW_SIZE = 30

# Load model
def load_model_compat(produk_key):
    keras_path = os.path.join(MODELS_DIR, f'lstm_{produk_key}_model.keras')
    h5_path    = os.path.join(MODELS_DIR, f'lstm_{produk_key}_model.h5')

    if os.path.exists(keras_path):
        return tf.keras.models.load_model(keras_path, compile=False)

    model = tf.keras.Sequential([
        tf.keras.layers.Input(shape=(WINDOW_SIZE, 1)),
        tf.keras.layers.LSTM(60, return_sequences=False, activation='tanh',
                             recurrent_activation='sigmoid', use_bias=True,
                             unit_forget_bias=True),
        tf.keras.layers.Dense(1, activation='linear'),
    ])
    model.load_weights(h5_path, by_name=False)
    return model

# MAPE
def calculate_mape(actual, predicted):
    actual    = np.array(actual).flatten()
    predicted = np.array(predicted).flatten()
    mask      = actual != 0
    if mask.sum() == 0:
        return None
    ape = np.abs((actual[mask] - predicted[mask]) / actual[mask]) * 100
    return float(np.mean(ape))

def load_mape_from_test_data(produk_key, model, scaler):

    test_path  = os.path.join(MODELS_DIR, f'test_data_{produk_key}.npy')
    train_path = os.path.join(MODELS_DIR, f'train_data_{produk_key}.npy')

    if not os.path.exists(test_path):
        return None

    test_values = np.load(test_path)
    if len(test_values) <= WINDOW_SIZE:
        return None

    if os.path.exists(train_path):
        train_values = np.load(train_path)
        split_idx    = len(train_values)
        all_values   = np.concatenate([train_values, test_values])
        all_scaled   = scaler.transform(all_values.reshape(-1, 1)).flatten()

        X_all, y_all = [], []
        for i in range(len(all_scaled) - WINDOW_SIZE):
            X_all.append(all_scaled[i : i + WINDOW_SIZE])
            y_all.append(all_values[i + WINDOW_SIZE])
        X_all = np.array(X_all)
        y_all = np.array(y_all)

        X_test = X_all[split_idx - WINDOW_SIZE:]
        y_test = y_all[split_idx - WINDOW_SIZE:]
    else:
        test_scaled = scaler.transform(test_values.reshape(-1, 1)).flatten()
        X_test, y_test = [], []
        for i in range(len(test_scaled) - WINDOW_SIZE):
            X_test.append(test_scaled[i : i + WINDOW_SIZE])
            y_test.append(test_values[i + WINDOW_SIZE])
        X_test = np.array(X_test)
        y_test = np.array(y_test)

    if len(X_test) == 0:
        return None

    actuals, preds = [], []
    for i in range(len(X_test)):
        seq_in   = X_test[i].reshape(1, WINDOW_SIZE, 1)
        pred_s   = model.predict(seq_in, verbose=0)
        pred_v   = scaler.inverse_transform(pred_s)[0][0]
        actuals.append(y_test[i])
        preds.append(pred_v)

    return calculate_mape(actuals, preds)

def predict(produk, lead_time=7, z_score=1.65, forecast_days=30, history=None):
    produk_key    = produk.lower().replace(' ', '_')
    scaler_path   = os.path.join(MODELS_DIR, f'scaler_{produk_key}.pkl')
    last_seq_path = os.path.join(MODELS_DIR, f'last_seq_{produk_key}.npy')

    model = load_model_compat(produk_key)

    with open(scaler_path, 'rb') as f:
        scaler = pickle.load(f)

    if history and len(history) >= WINDOW_SIZE:
        raw         = np.array(history[-WINDOW_SIZE:]).reshape(-1, 1)
        current_seq = scaler.transform(raw)
    elif os.path.exists(last_seq_path):
        current_seq = np.load(last_seq_path)
        if current_seq.shape != (WINDOW_SIZE, 1):
            current_seq = current_seq.reshape(WINDOW_SIZE, 1)
    else:
        raise FileNotFoundError(
            f'File last_seq_{produk_key}.npy tidak ditemukan di folder models/.'
        )

    mape = load_mape_from_test_data(produk_key, model, scaler)

    # Forecast ke depan
    predictions_scaled = []
    seq = current_seq.copy()
    for _ in range(forecast_days):
        pred = model.predict(seq.reshape(1, WINDOW_SIZE, 1), verbose=0)
        predictions_scaled.append(pred[0][0])
        seq = np.append(seq[1:], pred.reshape(1, 1), axis=0)

    predictions = scaler.inverse_transform(
        np.array(predictions_scaled).reshape(-1, 1)
    ).flatten().tolist()

    avg_demand   = float(np.mean(predictions))
    std_demand   = float(np.std(predictions))
    safety_stock = z_score * std_demand * math.sqrt(lead_time)
    rop          = (avg_demand * lead_time) + safety_stock
    recommended  = (avg_demand * forecast_days) + safety_stock

    result = {
        'produk'           : produk,
        'rop'              : round(rop, 2),
        'recommended_order': round(recommended, 2),
    }

    if mape is not None:
        result['mape'] = round(mape, 2)

    return result

# CLI
if __name__ == '__main__':
    try:
        if '--file' in sys.argv:
            file_idx   = sys.argv.index('--file')
            input_path = sys.argv[file_idx + 1]
            with open(input_path, 'r') as f:
                input_data = json.load(f)
        else:
            input_data = json.loads(sys.argv[1])

        hasil = predict(**input_data)
        sys.stdout.write(json.dumps(hasil))
    except Exception as e:
        sys.stdout.write(json.dumps({'error': str(e)}))