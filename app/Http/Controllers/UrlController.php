<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use Yangqi\Htmldom\Htmldom;
use Imagick;
use Spipu\Html2Pdf\Html2Pdf;
use GuzzleHttp\Client;

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
        //$image = str_replace(array('_', '-'), array('/', '+'), $image);
        //return 'data:image/jpeg;base64,' . $image;
        return $image['lighthouseResult']['audits']['final-screenshot']['details']['data'];
        
        
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
            
            
            
            //Get the title from get_contedocn
            $title = $this->get_title($json);
            $rmetas['title'] = $title;
            
            
            
            /*   if (( (!array_key_exists('og:image', $rmetas) || $rmetas['og:image'] == '') &&  (!array_key_exists('og:image', $rmetas) || $rmetas['twitter:image:src'] == ''))) {
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
    public function getUrlData(request $request) // $raw - enable for raw display
    {
        
        $this->validate($request, [
        'url' => 'required|url',
        'raw' => 'boolean'
        ]);
        
        $raw = $request->input('raw', false);
        
        $result = false;
        
        $url = $request->input('url');
        
        $contents = $this->getUrlContents($url);
        
        if (isset($contents) && is_string($contents))
        {
            $title = null;
            $metaTags = null;
            $metaProperties = null;
            
            $title = $this->get_title($contents);
            
            //preg_match_all('/<[\s]*meta[\s]*(name|property)="?' . '([^>"]*)"?[\s]*' . 'content="?([^>"]*)"?[\s]*[\/]?[\s]*>/si', $contents, $match);
            preg_match_all('/<meta.*(name|property)="(.*)".*content="(.*)".*>/siU', $contents, $match);
            
            if (isset($match) && is_array($match) && count($match) == 4)
            {
                $originals = $match[0];
                $names = $match[2];
                $values = $match[3];
                
                if (count($originals) == count($names) && count($names) == count($values))
                {
                    $metaTags = array();
                    $metaProperties = $metaTags;
                    
                    
                    for ($i=0, $limiti=count($names); $i < $limiti; $i++)
                    {
                        /*  if ($match[1][$i] == 'name')
                        $meta_type = 'metaTags';
                        else
                        $meta_type = 'metaProperties';
                        if ($raw)
                        ${$meta_type}[$names[$i]] = array (
                        'html' => htmlentities($originals[$i], $flags, 'UTF-8'),
                        'value' => $values[$i]
                        );
                        ${$meta_type}[$names[$i]] = $values[$i];
                        else
                        ${$meta_type}[$names[$i]] = array (
                        'html' => $originals[$i],
                        'value' => $values[$i]
                        );
                        ${$meta_type}[$names[$i]] = $values[$i];*/
                        
                        ${'data'}[$names[$i]] = $values[$i];
                    }
                }
            }
            
            $result = array (
            'title' => $title,
            'meta' => $data,
            //'metaProperties' => $names,
            // 'metaTags' => $metaTags,
            // 'metaProperties' => $metaProperties,
            );
        }
        return response()->json(['valid' => true, 'msg' => 'url found', 'data'=>$result], 200);
    }
    
    private function getUrlContents($url, $maximumRedirections = null, $currentRedirection = 0)
    {
        $result = false;
        
        $contents = @file_get_contents($url);
        
        // Check if we need to go somewhere else
        
        if (isset($contents) && is_string($contents))
        {
            preg_match_all('/<[\s]*meta[\s]*http-equiv="?REFRESH"?' . '[\s]*content="?[0-9]*;[\s]*URL[\s]*=[\s]*([^>"]*)"?' . '[\s]*[\/]?[\s]*>/siU', $contents, $match);
            
            if (isset($match) && is_array($match) && count($match) == 2 && count($match[1]) == 1)
            {
                if (!isset($maximumRedirections) || $currentRedirection < $maximumRedirections)
                {
                    return getUrlContents($match[1][0], $maximumRedirections, ++$currentRedirection);
                }
                
                $result = false;
            }
            else
            {
                $result = $contents;
            }
        }
        
        return $contents;
    }
    
    private function get_title($html)
    {
        preg_match("/<title(.+)<\/title>/siU", $html, $matches);
        if( !empty( $matches[1] ) )
        {
            $title = $matches[1];
            
            if( strstr($title, '>') )
            {
                $title = explode( '>', $title, 2 );
                $title = $title[1];
                
                return trim($title);
            }
        }
    }
    
    
    private function imgToBase64($path) {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = @file_get_contents($path);
        if($data === FALSE)
        {
            $url = $url = explode('/',$this->file);
            array_pop($url);
            $url = implode('/', $url);
            $data = @file_get_contents($url."/".$path);
            if($data === FALSE)
            return false;
            
            
        }
        
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }
    
    private function getOgtags($url)
    {
        $html=  file_get_contents($url);
        $pattern='/<\s*meta\s+property="og:([^"]+)"\s+content="([^"]*)/i';
        if(preg_match_all($pattern, $html, $out))
        return array_combine($out[1], $out[2]);
        return array();
    }
    
    /**
    * Html to PDF using dompdf
    */
    public function html2pdf(request $request)
    {
        $this->validate($request, [
        'url' => 'required|url'
        ]);
        
        $url = $request->input('url');
        
        
        $html=  file_get_contents($url);
        
        //$html = '<h1>tadaaa</h1>';
        
        $html2pdf = new Html2Pdf();
        $html2pdf->writeHTML($html);
        $html2pdf->output(base_path().'/storage/app/temp.pdf','F');
        
        
        $im = new imagick(base_path().'/storage/app/temp.pdf');
        $im->setImageFormat( "jpg" );
        $img_name = time().'.jpg';
        $im->setSize(800,600);
        $imgstring = $im->__toString();
        //$im->writeImage($img_name);
        $im->clear();
        $im->destroy();
        
        $html2pdf->clean();
        return $imgstring;
    }
    
    /**
    * Peekalink
    */
    public function peekalink(request $request)
    {
        //$client = new \Guzzle\Service\Client('http://api.github.com/users/');
        
        
        /*  response = requests.post(
        "https://api.peekalink.io/",
        headers={"X-API-Key": "YourSecretKey"},
        data={"link": "https://bit.ly/3frD2OP"},
        );
        
        return $client; */
        
        // $response = $client->get("users/$username")->send();
        
        $url = $request->input('url');
        
        $client = new Client();
        // $client->setDefaultOption('headers', array('X-API-Key' => env('PEEKALINK_API')));
        $res = $client->request('POST', 'https://api.peekalink.io/', [
        'debug' => TRUE,
        'headers' => [
        'Content-Type' => 'application/x-www-form-urlencoded',
        'X-API-Key' => env('PEEKALINK_API')
        ],
        'form_params' => [
        'data' => $url,
        ]
        ]);
        // echo $res->getStatusCode();
        // 200
        //   echo $res->getHeader('X-API-Key');
        // 'application/json; charset=utf8'
        return $res->getBody();
        // {"type":"User"...'
        
        
    }
}