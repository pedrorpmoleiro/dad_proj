<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'customer_id',
        'notes',
        'total_price',
        'date',
        'opened_at',
        'current_status_at'
    ];

    public function items()
    {
        return $this->belongsToMany(Product::class, 'order_items', 'order_id', 'product_id')
            ->using(OrderItem::class)->withPivot(['quantity', 'unit_price', 'sub_total_price']);
    }
}
