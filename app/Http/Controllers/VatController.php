<?php

/**
 * Class Controller
 * @package App\Http\Controllers
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="1.0.0",
 *         title="ApiFifteen - VAT utilities",
 *         @OA\License(name="MIT")
 *     ),
 *     @OA\Server(
 *         description="API server",
 *         url="http://api.fifteenpeas.com/",
 *     ),
 * )
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;


use SoapClient;

class VatController extends Controller
{

    public $address;


    /**
     * @param string $code
     * @return bool
     */
    public function isCountryCodeInEU(string $code): bool
    {
        $eu = ['AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HU', 'HR', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK'];
        return in_array($code, $eu);
    }


    /**
     * @OA\Post(
     *     path="v1/vat/checkvat",
     *     summary="Check VAT number against VIES service from European Commission",
     *     operationId="checkVat",
     *     tags={"VAT"},
     *     @OA\Parameter(
     *         name="vat",
     *         required=true,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(
     *                 type="string"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A json fragment with the company data found by vat number",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/PostResponse")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function checkVat(request $request)
    {
        $this->validate($request, [
            'vat' => 'required',
        ]);

        $vatid = $request->input('vat');

        $vats = [
            'BE', 'BG',
            'CZ', 'DK', 'DE', 'EE', 'EL', 'ES', 'FR',
            'GB', 'HR', 'IE', 'IT', 'CY', 'LV',
            'LT', 'LU', 'HU', 'MT', 'NL', 'AT', 'PL', 'PT', 'RO', 'SI', 'SK', 'FI',
            'SE', 'UK',
        ];

        $vatid = strtoupper(str_replace(['',  '.',  '-',  ',',  ', ', ' '],  '', trim($vatid)));
        $cc = strtoupper(substr($vatid,  0,  2));
        $vn = substr($vatid,  2);
        $outp = [];

        $client = new \SoapClient('http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl', ['trace' => true, 'cache_wsdl' => WSDL_CACHE_MEMORY]);

        if ($client) {

            $paramsApi = ['countryCode'  => $cc,  'vatNumber'  => $vn];
            // $paramsApi = [ 'name'  => 'FIFTEENPEAS IT S.A R.L.']; 
            try {

                //test
                if (in_array($cc, $vats)) //TODO compare lower case
                    $r = $client->checkVat($paramsApi);
                else {
                    return response()->json(['valid' => false, 'msg' => 'Invalid parameters given.']);
                }

                if (isset($r) && ($r->valid ==  true) && (!is_null($vn))) {

                    // VAT-ID is valid 
                    // This foreach shows every single line of the returned information 
                    foreach ($r as $k => $prop) {
                        $outp[$k] = $prop;
                    }

                    $outp['isEU'] = $this->isCountryCodeInEU($cc);


                    /*  if ($decodeAddress) {
                                            $data = Yii::$app->commandBus->handle(new \common\google\commands\DecodeAddressCommand([
                                                'address'=>$r->address
                                            ]));
                                            $data['name'] = $r->name;
                                            }
                                        else
                                            $data = [];*/


                    return  response()->json(['valid' => true, 'msg' => 'Valid VAT number', 'data' => $outp]);
                } else {
                    return response()->json(['valid' => false, 'msg' => 'Invalid VAT number', 'data' => $outp]);
                }
            } catch (SoapFault $e) {
                $outp[] = $e->faultstring;
                return response()->json(['valid' => false, 'msg' => 'An unexpected error happened', 'data' => $outp]);
            }
        } else {
            return response()->json(['valid' => false, 'msg' => 'Connection to EUVAT failed ! Please, try again later.']);
        }
    }


    public function locateIpAddress(request $request): string
    {

        $this->validate($request, [
            'ipAddress' => 'ip',
        ]);

        $ipAddress = $request->input('ipAddress', 'self');

        if ($ipAddress === '') {
            return '';
        }

        $client = new \GuzzleHttp\Client();
        $request = $client->get('https://ip2c.org/' . $ipAddress);
        $response = $request->getBody();

        return $response;
    }

    public function ipVatRate(request $request)//: string
    {
        $data = $this->locateIpAddress($request);
        $json = file_get_contents('https://raw.githubusercontent.com/ibericode/vat-rates/master/vat-rates.json');
        $arr = explode(';', $data);

        return $this->searchJson($json,$arr[1]);
    }


    public function searchJson( $obj, $value ) {
        $obj = json_decode($obj,false);
       foreach( $obj->items as $key => $item ) {
            if(  $key == $value ){
                 return $item;
            } 
        }
       
        return false;
    }

    /**
     *  Returns all vat rates in json
     * 
     * @return json 
     *  
     */
    public function vatRates()
    {
        $json = file_get_contents('https://raw.githubusercontent.com/ibericode/vat-rates/master/vat-rates.json');
        return $json;
    }

    function getallheaders()
    {
        $headers = array();

        $copy_server = array(
            'CONTENT_TYPE'   => 'Content-Type',
            'CONTENT_LENGTH' => 'Content-Length',
            'CONTENT_MD5'    => 'Content-Md5',
        );

        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $key = substr($key, 5);
                if (!isset($copy_server[$key]) || !isset($_SERVER[$key])) {
                    $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                    $headers[$key] = $value;
                }
            } elseif (isset($copy_server[$key])) {
                $headers[$copy_server[$key]] = $value;
            }
        }

        if (!isset($headers['Authorization'])) {
            if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
                $basic_pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
                $headers['Authorization'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $basic_pass);
            } elseif (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                $headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
            }
        }

        return $headers;
    }
}
