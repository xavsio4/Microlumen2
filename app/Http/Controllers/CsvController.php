<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\OrderShipped;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Mailgun\Mailgun;

class CsvController extends Controller
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
    
    /*
    * Converts CSV File to JSON PHP Script
    * Example uses Google Spreadsheet CSV
    */
    public function Csv2json(request $request)
    {
        
        $this->validate($request, [
        'source' => 'required',
        ]);
        
        
        //Set your file path here
        
        //$filePath = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTEKCTdbMgSEt7UCymQ956PIYsHei51gpCtPou4VGugKRztJVuZSNuDXKDrdDiZxx6-Ebepte8P6OlG/pub?output=csv';
        
        $filePath = $request->input('source');
        // define two arrays for storing values
        $keys = array();
        $newArray = array();
        
        
        // Call the function convert csv To Array
        $data = this->convertCsvToArray($filePath, ',');
        
        // Set number of elements (minus 1 because we shift off the first row)
        $count = count($data) - 1;
        
        //First row for label or name
        $labels = array_shift($data);
        foreach ($labels as $label) {
            $keys[] = $label;
        }
        
        // assign keys value to ids, we add new parameter id here
        $keys[] = 'id';
        for ($i = 0; $i < $count; $i++) {
            $data[$i][] = $i;
        }
        
        // combine both array
        for ($j = 0; $j < $count; $j++) {
            $d = array_combine($keys, $data[$j]);
            $newArray[$j] = $d;
        }
        
        // convert array to json php using the json_encode()
        $arrayToJson = json_encode($newArray);
        
        // print converted csv value to json
        return $arrayToJson;
        
    }
    
    //PHP Function to convert CSV into array
    public function convertCsvToArray($file, $delimiter) {
        $handle = $file;
        $i = 0;
        while (($lineArray = fgetcsv($handle, 4000, $delimiter, '"')) !== FALSE) {
            for ($j = 0; $j < count($lineArray); $j++) {
                $arr[$i][$j] = $lineArray[$j];
            }
            $i++;
        }
        fclose($handle);
        
        return $arr;
    }
    
    
    
    //
}