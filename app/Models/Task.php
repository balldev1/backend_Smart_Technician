<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'required_skill',
        'urgency',
        'duration',
        'required_technicians',
    ];
}
