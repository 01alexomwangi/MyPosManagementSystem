<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
     protected $fillable = [
        'type',
        'payload'
    ];
}
