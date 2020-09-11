<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Measure;

class MeasureController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function create(request $request)
    {
        $measure = Measure::create($request->all());

        return response()->json($measure, 201);
    }
}
