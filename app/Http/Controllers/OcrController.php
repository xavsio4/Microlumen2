<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;

class OcrController extends Controller
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

    /**
     * 
     */
    public function index(request $request)
    {
        $tesseract = new TesseractOCR(app()->basePath('public/img/factmars.png'));
 $result = $tesseract->run();
 echo $result;
    }
}
