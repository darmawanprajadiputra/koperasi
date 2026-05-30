"""
convert_model.py
────────────────
Bangun ulang arsitektur LSTM dari config, load weight dari .h5 lama,
lalu simpan ke format .keras baru yang kompatibel dengan Keras 3.x

Jalankan SEKALI:
    python ml/convert_model.py
"""

import os
os.environ['TF_CPP_MIN_LOG_LEVEL']  = '3'
os.environ['TF_ENABLE_ONEDNN_OPTS'] = '0'

import warnings
warnings.filterwarnings('ignore')

import tensorflow as tf
import numpy as np

MODELS_DIR     = 'ml/models'
OLD_MODEL_PATH = os.path.join(MODELS_DIR, 'lstm_beras_model.h5')
NEW_MODEL_PATH = os.path.join(MODELS_DIR, 'lstm_beras_model.keras')

print('Membangun ulang arsitektur model...')

model = tf.keras.Sequential([
    tf.keras.layers.Input(shape=(30, 1)),
    tf.keras.layers.LSTM(60, return_sequences=False, activation='tanh',
                         recurrent_activation='sigmoid', use_bias=True,
                         unit_forget_bias=True),
    tf.keras.layers.Dense(1, activation='linear'),
], name='sequential')

print('Arsitektur berhasil dibangun.')
model.summary()

print('\nMemuat weight dari model lama...')
model.load_weights(OLD_MODEL_PATH, by_name=False)
print('Weight berhasil dimuat.')

print(f'\nMenyimpan model baru ke: {NEW_MODEL_PATH}')
model.save(NEW_MODEL_PATH)
print('Konversi selesai!')

# Verifikasi: coba load model baru
print('\nVerifikasi model baru...')
model_baru = tf.keras.models.load_model(NEW_MODEL_PATH)
dummy = np.zeros((1, 30, 1))
hasil = model_baru.predict(dummy, verbose=0)
print(f'Test prediksi berhasil. Output: {hasil}')
print('\nModel siap digunakan.')