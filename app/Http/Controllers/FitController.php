<?php
/**
* This controller updates the hitcount models
* which is a simple counter system that can be applied
* to pages or actions
* The client has to manage the list of the endpoints.
*
* Mandatory parameter is the domain
* Optional is item which can be
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Hitcount;

use adriangibbons\phpFITFileAnalysis;



class FitController extends Controller
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
        // return $request->file();
        
        $this->validate($request, [
        'file' => 'required'
        ]);
        
        //$fileModel = new File;
        
        if($request->file()) {
            //return 'plouf';
            $fileName = time().'_'.$request->file->getClientOriginalName();
            $path = ".." . DIRECTORY_SEPARATOR .'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR;
            //$destinationPath = public_path($path); // upload path
            $file = $request->file('file')->move($path, $fileName);
            
            //$filePath = $request->file('file')->store('/storage/gpx/');
            //$filePath = $request->file('file')->storeAs('/storage/gpx/', $fileName, 'public');
            
            //  $fileModel->name = time().'_'.$request->file->getClientOriginalName();
            //  $fileModel->file_path = '/storage/' . $filePath;
            //  $fileModel->save();
            
            $result = $this->parseFile($file);
            
            return response()->json(['status' => 'success', 'data' => $result], 200);
        }
        return response()->json(['status' => 'failed', 'data' => $request], 400);
    }
    
    
    /**
    * Parse a GPX File
    */
    public function parseFile($file)
    {
        $stats = [];
        $distance = 0;
        $nbrTracks = 0;
        $cumulativeElevationGain = 0;
        $output = [];
        //$path = ".." . DIRECTORY_SEPARATOR .'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR.'EV3.gpx';

        $pFFA = new phpFITFileAnalysis($file);
        
        
        
       /* $file = $gpx->load($file);
        
        foreach ($file->tracks as $track)
        {
            // Statistics for whole track
            $track->stats->toArray();
            array_push($stats,$track->stats->toArray());
            
            foreach ($track->segments as $segment)
            {
                // Statistics for segment of track
                $segment->stats->toArray();
            }
        } */
        
        //total distance
      /*  foreach ($stats as $stat)
        {
            $output = $stat['distance'];
            $distance += $stat['distance'];
            $cumulativeElevationGain += $stat['cumulativeElevationGain'];
        }
        
        $nbrTracks = count($stats); */
        
        return $pFFA->data_mesgs;
    }
     
   
}