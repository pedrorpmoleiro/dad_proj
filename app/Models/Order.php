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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function cook()
    {
        return $this->belongsTo(User::class, 'prepared_by', 'id');
    }

    public function delivery_man()
    {
        return $this->belongsTo(User::class, 'delivered_by', 'id');
    }
}
