<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeoController extends Controller
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
    public function getDistance(request $request)
    {
        $google_api =  env('GOOGLE_API'));
        $this->validate($request, [
            'start' => 'required',
            'destination' => 'required'
        ]);

        if ($request->input('start') && $request->input('destination')) {
            $destination = $request->input('destination');
            $start = $request->input('start');
            $departure_time = 'now';
            $apiUrl = 'https://maps.googleapis.com/maps/api/distancematrix/json';
            $url = $apiUrl . '?' . 'origins=' . urlencode($start) . '&destinations=' . 
            urlencode($destination) . '&departure_time=' . urlencode($departure_time) . '&key=' . $google_api;
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $res = curl_exec($curl);
            if (curl_errno($curl)) {
                throw new Exception(curl_error($curl));
            }
            curl_close($curl);
            $json = json_decode(trim($res), true);
            return $json;
        } else return false;
    }
}
