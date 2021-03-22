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
    
    public function filein(request $request)
    {
        $this->validate($request, [
        'file' => 'required|max:2048'
        ]);
        
        //$fileModel = new File;
        
        if($request->file()) {
            $fileName = time().'_'.$request->file->getClientOriginalName();
            $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');
            
            //  $fileModel->name = time().'_'.$request->file->getClientOriginalName();
            //  $fileModel->file_path = '/storage/' . $filePath;
            //  $fileModel->save();
            
            
            return response()->json(['status' => 'success', 'data' => $fileName], 200);
        }
        return response()->json(['status' => 'failed', 'data' => $request], 205);
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