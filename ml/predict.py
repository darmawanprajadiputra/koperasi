import sys
import json
import numpy as np
import pickle
import math
import os
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'  # suppress tensorflow warnings
from tensorflow.keras.models import load_model

MODELS_DIR = os.path.join(os.path.dirname(__file__), 'models')
WINDOW_SIZE = 30

def predict(produk, history, lead_time=7, z_score=1.65, forecast_days=30):
    produk_key  = produk.lower().replace(' ', '_')
    model_path  = os.path.join(MODELS_DIR, f'lstm_{produk_key}_model.h5')
    scaler_path = os.path.join(MODELS_DIR, f'scaler_{produk_key}.pkl')

    model = load_model(model_path)
    with open(scaler_path, 'rb') as f:
        scaler = pickle.load(f)

    last_sequence = np.array(history[-WINDOW_SIZE:]).reshape(-1, 1)
    current_seq   = scaler.transform(last_sequence)

    predictions_scaled = []
    for _ in range(forecast_days):
        pred = model.predict(current_seq.reshape(1, WINDOW_SIZE, 1), verbose=0)
        predictions_scaled.append(pred[0][0])
        current_seq = np.append(current_seq[1:], pred.reshape(1, 1), axis=0)

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
        input_data = json.loads(sys.argv[1])
        hasil = predict(**input_data)
        print(json.dumps(hasil))  # fix: json.encode → json.dumps
    except Exception as e:
        print(json.dumps({'error': str(e)}))