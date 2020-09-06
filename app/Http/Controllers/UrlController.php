<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;

class UrlController extends Controller
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
     * 
     * @return String
     */
    function curl_get_contents($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public static function Utf8_ansi($value = '')
    {

        $utf8_ansi2 = array(
            "\u00c0" => "À",
            "\u00c1" => "Á",
            "\u00c2" => "Â",
            "\u00c3" => "Ã",
            "\u00c4" => "Ä",
            "\u00c5" => "Å",
            "\u00c6" => "Æ",
            "\u00c7" => "Ç",
            "\u00c8" => "È",
            "\u00c9" => "É",
            "\u00ca" => "Ê",
            "\u00cb" => "Ë",
            "\u00cc" => "Ì",
            "\u00cd" => "Í",
            "\u00ce" => "Î",
            "\u00cf" => "Ï",
            "\u00d1" => "Ñ",
            "\u00d2" => "Ò",
            "\u00d3" => "Ó",
            "\u00d4" => "Ô",
            "\u00d5" => "Õ",
            "\u00d6" => "Ö",
            "\u00d8" => "Ø",
            "\u00d9" => "Ù",
            "\u00da" => "Ú",
            "\u00db" => "Û",
            "\u00dc" => "Ü",
            "\u00dd" => "Ý",
            "\u00df" => "ß",
            "\u00e0" => "à",
            "\u00e1" => "á",
            "\u00e2" => "â",
            "\u00e3" => "ã",
            "\u00e4" => "ä",
            "\u00e5" => "å",
            "\u00e6" => "æ",
            "\u00e7" => "ç",
            "\u00e8" => "è",
            "\u00e9" => "é",
            "\u00ea" => "ê",
            "\u00eb" => "ë",
            "\u00ec" => "ì",
            "\u00ed" => "í",
            "\u00ee" => "î",
            "\u00ef" => "ï",
            "\u00f0" => "ð",
            "\u00f1" => "ñ",
            "\u00f2" => "ò",
            "\u00f3" => "ó",
            "\u00f4" => "ô",
            "\u00f5" => "õ",
            "\u00f6" => "ö",
            "\u00f8" => "ø",
            "\u00f9" => "ù",
            "\u00fa" => "ú",
            "\u00fb" => "û",
            "\u00fc" => "ü",
            "\u00fd" => "ý",
            "\u00ff" => "ÿ"
        );

        return strtr($value, $utf8_ansi2);
    }

    /**
     * Convert html to image
     * 
     */
    public function getScreenshot(request $request)
    {
        $this->validate($request, [
            'url' => 'required|url',
            'screenshot' => 'boolean'
        ]);

        $url = $request->input('url');


        $image = file_get_contents("https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=$url");
        $image = json_decode($image, true);
        $image = $image['loadingExperience']['screenshot-thumbnails']['details']['items'][2];
        $image = str_replace(array('_', '-'), array('/', '+'), $image);
        return 'data:image/jpeg;base64,' . $image;
    }

    /**
     * Gets the meta data of the provided url
     * 
     * Return a json string containing the page header parsed
     * 
     * 
     * @return Json 
     * 
     * @param String url
     * @param  Boolean screenshot (optional default 0)
     */
    public function fetchMeta(request $request)
    {

        $this->validate($request, [
            'url' => 'required|url',
            'screenshot' => 'boolean'
        ]);

        //$html= ''; 

        $url = $request->input('url');
        $ws = $request->input('screenshot', 0);

        $json = $this->curl_get_contents($url);


        if ($json) {

            $rmetas = get_meta_tags($request->input('url'));

            foreach ($rmetas as $key => $item) {
                $rmetas[$key] = $this->Utf8_ansi($item);
            }


            /*   if (( (!array_key_exists('og:image', $rmetas) || $rmetas['og:image'] == '') &&  (!array_key_exists('og:image', $rmetas) || $rmetas['twitter:image'] == ''))) {
                $image = file_get_contents("https://www.googleapis.com/pagespeedonline/v2/runPagespeed?url=$url&screenshot=true");
                $image = json_decode($image, true);
                $image = $image['screenshot']['data'];
                $image = str_replace(array('_', '-'), array('/', '+'), $image);

                $rmetas['image'] = 'data:image/jpeg;base64,' . $image;
            } else
                $rmetas['image'] = ($rmetas['og:image']) ? $rmetas['og:image'] : $rmetas['twitter:image'];
*/

            return response()->json(['valid' => true, 'msg' => 'Url found.', 'data' => $rmetas], 200);
        } else {
            return response()->json(['valid' => false, 'msg' => 'Not a valid url'], 200);
        }
    }
}
