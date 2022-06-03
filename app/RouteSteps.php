<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RouteSteps extends Model
{
    
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
    'name', 'distance','cumulative_distance','elevationgain',
    'elevationloss','added_flag','icon','notes','longstart','longend','latstart','latend'
    ];
    
    
}