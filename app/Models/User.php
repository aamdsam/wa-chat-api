<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'email', 'password','token',
    ];

    protected $hidden = [
        'password',
    ];
}
