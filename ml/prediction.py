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

def load_model_compat(produk_key):
    keras_path = os.path.join(MODELS_DIR, f'lstm_{produk_key}_model.keras')
    h5_path    = os.path.join(MODELS_DIR, f'lstm_{produk_key}_model.h5')

    if os.path.exists(keras_path):
        return tf.keras.models.load_model(keras_path)

    # Fallback: bangun ulang arsitektur + load weight dari .h5
    model = tf.keras.Sequential([
        tf.keras.layers.Input(shape=(WINDOW_SIZE, 1)),
        tf.keras.layers.LSTM(60, return_sequences=False, activation='tanh',
                             recurrent_activation='sigmoid', use_bias=True,
                             unit_forget_bias=True),
        tf.keras.layers.Dense(1, activation='linear'),
    ])
    model.load_weights(h5_path, by_name=False)
    return model

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

    predictions_scaled = []
    seq = current_seq.copy()
    for _ in range(forecast_days):
        pred = model.predict(seq.reshape(1, WINDOW_SIZE, 1), verbose=0)
        predictions_scaled.append(pred[0][0])
        seq = np.append(seq[1:], pred.reshape(1, 1), axis=0)

    predictions  = scaler.inverse_transform(
        np.array(predictions_scaled).reshape(-1, 1)
    ).flatten().tolist()

    avg_demand   = float(np.mean(predictions))
    std_demand   = float(np.std(predictions))
    safety_stock = z_score * std_demand * math.sqrt(lead_time)
    rop          = (avg_demand * lead_time) + safety_stock
    recommended  = (avg_demand * forecast_days) + safety_stock

    return {
        'produk'           : produk,
        'rop'              : round(rop, 2),
        'recommended_order': round(recommended, 2),
    }

if __name__ == '__main__':
    try:
        # Baca input dari --file (temp JSON file) atau dari argv[1]
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