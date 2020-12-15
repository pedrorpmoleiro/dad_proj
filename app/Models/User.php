<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\CanResetPassword;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'blocked',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function toStdClass(): \stdClass
    {
        $user = new \stdClass();

        $user->id = $this->id;
        $user->name = $this->name;
        $user->email = $this->email;
        $user->type = $this->type;
        $user->blocked = $this->blocked;
        $user->photo_url = $this->photo_url;

        return $user;
    }

    public function addToStdClass($user): \stdClass
    {
        $user->id = $this->id;
        $user->name = $this->name;
        $user->email = $this->email;
        $user->type = $this->type;
        $user->blocked = $this->blocked;
        $user->photo_url = $this->photo_url;

        return $user;
    }
}
