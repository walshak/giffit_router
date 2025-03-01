<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Router extends Model
{
    protected $fillable = [
        'name',
        'desc',
        'ip_address',
        'port',
        'username',
        'password'
    ];
}
