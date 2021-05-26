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

class HitcountController extends Controller
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
        $hitcount = Hitcount::create($request->all());
        
        return response()->json($measure, 201);
    }
    
    public function hit(request $request,$domain,$item=null)
    {
        /*$this->validate($request, [
        'domain' => 'required',
        ]);*/
        // $item = $request->input('item');
        // $domain = $request->input('domain');
        //if item is set
        if ($domain) {
            
            
            
            if (
                $hitcount = Hitcount::where('domain', $domain)->where('item',$item)
            ->first()
            ) {
                
                $hitcount->count = $hitcount->count + 1;
                $hitcount->save();
                
            }
            else {
                $hitcount = new Hitcount();
                $hitcount->domain = $domain;
                $hitcount->item = $item;
                $hitcount->save();
            }
            
            return $hitcount->count;
        }
        
        return false;
    }
    
    public function view(request $request)
    {
        $hitcount = Hitcount::findOne(condition);
        
    }
}