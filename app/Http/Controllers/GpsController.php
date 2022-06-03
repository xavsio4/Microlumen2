<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use phpGPX\phpGPX;
use App\RouteSteps;

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
        $segments = [];
        $trk = [];
        $distance = 0;
        $nbrTracks = 0;
        $cumulativeElevationGain = 0;
        $output = [];
        $path = ".." . DIRECTORY_SEPARATOR .'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR.'EV3.gpx';
        
        $gpx = new phpGPX();
        
        $file = $gpx->load($file);
        
        foreach ($file->tracks as $track)
        {
            // Statistics for whole track
            $track->stats->toArray();
            array_push($stats,$track->stats->toArray());
            array_push($trk,array('point'=>stripslashes($track->name),'stats'=>$track->stats->toArray()));

            $segments = [];
            foreach ($track->segments as $segment)
            {
                // Statistics for segment of track
                $segment->stats->toArray();
                array_push($segments,$segment);
            }
            
            
            if($track->name) {
            $routeStep = New RouteSteps();
            $routeStep->name = stripslashes($track->name);
            $routeStep->distance = $track->stats->distance;
            $routeStep->elevationgain = $track->stats->cumulativeElevationGain;
            $routeStep->elevationloss = $track->stats->cumulativeElevationLoss;
            $routeStep->longstart = $segments[0]->points->longitude;
            $routeStep->added_flag = 0;
            $routeStep->save();
            }
            
            
            
        }
        
        //total distance
        foreach ($stats as $stat)
        {
            $output = $stat['distance'];
            $distance += $stat['distance'];
            $cumulativeElevationGain += $stat['cumulativeElevationGain'];
        }
        
        $nbrTracks = count($stats);
        //return $trk;
        return $segments;
        //return $file->metadata;
        //.'Total elevation: '.number_format($cumulativeElevationGain).' Total distance: '.number_format($distance/1000, 2).' km, Nb tracks: '.$nbrTracks;
    }
}