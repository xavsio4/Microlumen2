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
        Mail::to($request->user())
   // ->cc($moreUsers)
   // ->bcc($evenMoreUsers)
    ->send(new OrderShipped($order));
    }

    public function SendMailgun(request $request)
    {
        $environment = app()->environment();
        $api = env('MAILGUN_API', true);
        $domain = env('APP_DOMAIN', true);
      
     $mgClient = Mailgun::create($api);
# Make the call to the client.
$result = $mgClient->messages()->send($domain, array(
	'from'	=> 'Fifteenpeas Microservices User <mailgun@api.fifteenpeas.com>',
	'to'	=> 'Baz <xavier@fifteenpeas.com>',
	'subject' => 'Hello',
	'text'	=> 'Testing some Mailgun awesomness!'
)); 
    return response()->json($result, 201);


    }

    //
}
