<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'num_factur',
        'name_customer',
        'no_telephone',
        'address',
        'recipient',
        'id_products',
        'total_item',
        'total_amount',
        'payment_method',
        'payment_status',
        'notes',
    ];

    protected $casts = [
        'total_item'   => 'integer',
        'total_amount' => 'decimal:2',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /**
     * Get the product associated with this transaction.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'id_products', 'id');
    }
}