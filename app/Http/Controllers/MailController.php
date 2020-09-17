<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\OrderShipped;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Mailgun\Mailgun;

class MailController extends Controller
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

    public function SendMailSmtp(request $request)
    {
        
        $this->validate($request, [
            'source' => 'required|url',
            'subject' => 'required',
            'content' => 'required'
        ]);
        
        $domain = env('APP_DOMAIN', true);
        
        $data = [
                'source'=> $request->input('source'),
                'subject' => $request->input('subject'),
                'content' => $request->input('content'),
                'sender' => env('MAIL_FROM_ADDRESS', true),
                'receiver' => env('MAIL_TO_ADDRESS', true)
            ]; 

           

    try{
        $result = Mail::send(['html'=>'mail'], $data, function($message){
        $message->from(env('MAIL_FROM_ADDRESS', true), env('MAIL_FROM_ADDRESS', true));
        $message->to(env('MAIL_TO_ADDRESS', true))->subject($data['subject']);
        });
       }
       catch(Exemption $e){
           return $e->getMessage();
        } 
       
        return $result;
    }

    public function SendMailgun(request $request)
    {
        $environment = app()->environment();
        $api = env('MAILGUN_API', true);
        $domain = env('APP_DOMAIN', true);
        $sender = env('MAIL_FROM_ADDRESS', true);
        $receiver = env('MAIL_TO_ADDRESS', true); 
      
     $mgClient = Mailgun::create($api);
# Make the call to the client.
$result = $mgClient->messages()->send($domain, array(
	'from'	=> $sender,
	'to'	=> $receiver,
	'subject' => 'Hello',
	'text'	=> 'Testing some Mailgun awesomness!'
)); 
    return response()->json($result, 201);


    }

    //
}
