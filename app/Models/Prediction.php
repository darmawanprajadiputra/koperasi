<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    protected $fillable = [
        'product_id',
        'rekomendasi_stok',
        'rop',
        'tanggal_prediksi',
        'mape',
    ];

    protected $casts = [
        'tanggal_prediksi' => 'date',
        'mape'             => 'float',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}