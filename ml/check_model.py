import os
os.environ['TF_CPP_MIN_LOG_LEVEL']  = '3'
os.environ['TF_ENABLE_ONEDNN_OPTS'] = '0'

import h5py
import json

model_path = 'ml/models/lstm_beras_model.h5'

with h5py.File(model_path, 'r') as f:
    cfg = json.loads(f.attrs['model_config'])

print(json.dumps(cfg, indent=2))