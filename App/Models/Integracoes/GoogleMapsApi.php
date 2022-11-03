<?php
namespace App\Models\Integracoes;
use GuzzleHttp\Client;
 
class GoogleMapsApi extends \App\Models\CrudInit
{
	private $base_uri = "https://maps.googleapis.com/";

	private $key = "";

    public function getGeocode($address){

        $client = new Client([
          
            'base_uri' => $this->base_uri,
            'http_errors' => false,
       
            'timeout'  => 30,
        ]);

        $response = $client->request('GET', "maps/api/geocode/json", [
            'headers' => [
                'accept' => 'application/json',
                
            ],
            'query' => [
                'address' => $address,
                'key' => $this->key,
            ]
        ]);
        return json_decode($response->getBody()->getContents(), true);

    }
   
}
