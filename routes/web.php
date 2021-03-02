<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// default route that gives the version of the lumen installed
//modified to provide an evasive message
$router->get('/', function () use ($router) {
    //return $router->app->version();
    return "Ho hey..how are you ? You...look lost..";
    
});

//generate a key for Lumen
$router->get('/key', function() {
    return \Illuminate\Support\Str::random(32);
});

$router->post('url/geturltest', ['uses' => 'UrlController@getUrlTest']);

$router->group(['prefix' => 'v1'], function () use ($router) {
    
    // will perform ocr with tesseract
    $router->get('ocr/me',  ['uses' => 'OcrController@index']);
    
    // will return the page meta data from an URL
    $router->get('mail/smt',  ['uses' => 'MailController@SendMailSmtp']);
    
    // will return the page meta data from an URL
    $router->post('measure/create',  ['uses' => 'MeasureController@create']);
    
    // will return the page meta data from an URL
    $router->get('url/fetchmeta',  ['uses' => 'UrlController@getUrlData']);
    
    // will return the page meta data from an URL
    $router->get('url/screenshot',  ['uses' => 'UrlController@getScreenshot']);
    
    // html to pdf to image
    $router->get('url/html2pdf',  ['uses' => 'UrlController@html2pdf']);
    
    // html to pdf to image
    $router->post('url/peekalink',  ['uses' => 'UrlController@peekalink']);
    
    // will check if vat number is valid or not and will return data for valid number
    $router->get('vat/checkvat',  ['uses' => 'VatController@checkVat']);
    
    // will return country code based on the ip address of the requestor
    $router->get('vat/locateip',  ['uses' => 'VatController@locateIpAddress']);
    
    // will return all available vatRates available
    $router->get('vat/vatrates',  ['uses' => 'VatController@vatRates']);
    
    // will return the vat rate of the located ip country address
    $router->get('vat/ipvatrate',  ['uses' => 'VatController@ipVatRate']);
    
    // will return the distance between two addresses
    $router->get('geo/distance',  ['uses' => 'GeoController@getDistance']);
    
    //will increment the counter of the two parameters passed as a combined key
    $router->get('hit/count/{domain}[/{item}]',  ['uses' => 'HitcountController@hit']);
    
    
    // default response whne no route is given
    $router->get('/', function () use ($router) {
        //return $router->app->version();
        return "Ho hey..how are you ? You...look lost..";
        
    });
    
    
    
    /*  $router->get('authors/{id}', ['uses' => 'UrlController@']);
    
    $router->post('authors', ['uses' => 'AuthorController@create']);
    
    $router->delete('authors/{id}', ['uses' => 'AuthorController@delete']);
    
    $router->put('authors/{id}', ['uses' => 'AuthorController@update']); */
});