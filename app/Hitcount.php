<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hitcount extends Model
{
    
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
    'domain', 'item','count'
    ];
    
    
}