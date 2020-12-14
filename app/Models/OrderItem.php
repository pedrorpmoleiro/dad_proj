<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderItem extends Pivot
{
    public $incrementing = true;
    public $timestamps = false;
    protected $table = "order_items";
}
