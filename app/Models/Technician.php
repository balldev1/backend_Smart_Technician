<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Technician extends Model
{
    protected $fillable = ['name', 'skills'];

    protected $casts = [
        'skills' => 'array',  // แปลง skills เป็น array
    ];
}
