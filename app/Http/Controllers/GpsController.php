<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GpsController extends Controller
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
    
    public function filein(resuest $request)
    {
        $req->validate([
        'file' => 'required|mimes:gpx|max:2048'
        ]);
        
        $fileModel = new File;
        
        if($req->file()) {
            $fileName = time().'_'.$req->file->getClientOriginalName();
            $filePath = $req->file('file')->storeAs('uploads', $fileName, 'public');
            
            $fileModel->name = time().'_'.$req->file->getClientOriginalName();
            $fileModel->file_path = '/storage/' . $filePath;
            $fileModel->save();
            
            
            return response()->json(['status' => 'success', 'data' => $fileName], 200);
        }
    }
    
}

/**
* Parse a GPX File
*/
public function parseFile(request $request)
{
    $output = '';
    $gpx = simplexml_load_file('e.gpx');
    /* foreach($xml->trk->trkseg->trkpt as $trkpt) {
    
    $namespaces = $trkpt->getNamespaces(true);
    $gpxtpx = $trkpt->extensions->children($namespaces['gpxtpx']);
    $hr = (string) $gpxtpx->TrackPointExtension->hr;
    $output .= '<pre>'.print_r($hr).'</pre>';
    }*/
    
    foreach($gpx->trk->trkseg->children() as $trkpts) {
        $output .= (string)$trkpts->extensions->children('gpxtpx',true)->TrackPointExtension->hr;
    }
}
}