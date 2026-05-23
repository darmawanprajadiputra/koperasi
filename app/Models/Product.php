<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name_product',
        'categories_id',
        'price',
        'stock',
        'unit_id',
        'image',
        'is_active',
    ];

    protected $casts = [
        'price'      => 'decimal:2',
        'is_active'  => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'categories_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function predictions()
    {
        return $this->hasMany(Prediction::class);
    }

    public function latestPrediction()
    {
        return $this->hasOne(Prediction::class)->latestOfMany();
    }
}