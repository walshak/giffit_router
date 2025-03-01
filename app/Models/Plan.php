<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'desc',
        'upload_speed',
        'download_speed',
        'time_limit'
    ];
}
