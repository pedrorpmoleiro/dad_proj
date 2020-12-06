<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'address',
        'phone',
        'nif',
    ];

    public function orders()
    {
        return $this->hasMany('App\Models\Order', 'customer_id');
    }

    public function toStdClass(): \stdClass {
        $customer = new \stdClass();

        $customer->id = $this->id;
        $customer->address = $this->address;
        $customer->phone = $this->phone;
        $customer->nif = $this->nif;

        return $customer;
    }

    public function addToStdClass($customer, $id): \stdClass {
        if ($id)
            $customer->id = $this->id;

        $customer->address = $this->address;
        $customer->phone = $this->phone;
        $customer->nif = $this->nif;

        return $customer;
    }
}
