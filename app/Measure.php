<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Measure extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'measure_type', 'measure_value'
    ];

   
}
