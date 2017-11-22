<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Http\Requests;
use GuzzleHttp\Client;
use Image;

class SiteController extends Controller
{
	function index()
	{		
		// Api Call
		$client = new Client();
        $res = $client->request('GET', 'http://content.guardianapis.com/search?api-key=test&amp;show-fields=thumbnail,headline');
        $data = $res->getBody();

        // Decoded Data
        $data = json_decode($data);
        foreach ($data->response->results as $key => $value) {
        	echo "webTitle - ".$value->webTitle."<br>";
        	echo "Type - ".$value->type."<br>";
        	echo "SectionName - ".$value->sectionName."<br>";
        	echo "Web Url - ".$value->webUrl."<br>";
        	echo " <a target='_blank' href='".env("APP_URL")."store-images?url=".$value->webUrl."'> Click here to store the images from this url </a> <br><br>";
        	
        }
	}

	function store_image(Request $request)
	{
		// echo $request->url;
		error_reporting(0);

		$client = new Client();
        $res = $client->request('GET', $request->url);
        $data = $res->getBody();

        $dom = new \DOMDocument;
        $internalErrors = libxml_use_internal_errors(true);
		$dom->loadHTML($data);
		$images = $dom->getElementsByTagName('img');
		foreach ($images as $image) {
		        // $image->setAttribute('src', 'http://example.com/' . $image->getAttribute('src'));
			// echo $image->getAttribute('src').'<br><br>';

			echo "<img src='".$image->getAttribute('src')."' /> <br><br>";

			// $image = explode("?", $image->getAttribute('src'));

			if (@getimagesize($image->getAttribute('src'))) {
				$path = $image->getAttribute('src');
				$format = explode("?", $image->getAttribute('src'));

				echo $filename = basename($format[0]);
				Image::make($path)->save(public_path('images/' . $filename));
			}	
		}
	}

	function store_test_image(Request $request)
	{
		$path = "https://images.alapattdiamonds.com/app-source/product-images/marvella-rainbow-bangle-27092017153631-main.jpg";
		echo $filename = basename($path);
		Image::make($path)->save(public_path('images/' . $filename));
	}
}