<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'address',
        'phone',
        'nif',
    ];

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
