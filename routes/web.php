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

$router->post('url/geturltest', ['uses' => 'UrlController@getUrlTest']);

$router->group(['prefix' => 'v1'], function () use ($router) {
    
    // will return the page meta data from an URL
    $router->get('url/fetchmeta',  ['uses' => 'UrlController@fetchMeta']);

    // will check if vat number is valid or not and will return data for valid number
    $router->get('vat/checkvat',  ['uses' => 'VatController@checkVat']);

    // will return country code based on the ip address of the requestor
    $router->get('vat/locateip',  ['uses' => 'VatController@locateIpAddress']);

    // will return all available vatRates available
    $router->get('vat/vatrates',  ['uses' => 'VatController@vatRates']);

    // will return the vat rate of the located ip country address
    $router->get('vat/ipvatrate',  ['uses' => 'VatController@ipVatRate']);

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


