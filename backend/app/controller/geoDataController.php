<?php

// include  '../model/model.php';


class geoDataController
{
    public function geoData()
    {
        $address = $_POST['address'] ?? null;
        
        $apiKey = '49c0df08-d075-452e-8a4b-929a37cbb752';

        $result = array (
            'Structure_address' => null,
            'cord' => null,
            'nearMetro' => null
        );

		$parameters = array(
		  'apikey' => $apiKey,
		  'geocode' => $address, 
		  'format' => 'json'
		);

		$response = file_get_contents('https://geocode-maps.yandex.ru/1.x/?'. http_build_query($parameters));
		$obj = json_decode($response, true);

        $structAddress = $obj['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['AddressDetails']['Country']['AddressLine'];

        $result['Structure_address'] = $structAddress;

      //  echo $response;


        $cord = str_replace(" ", ",", $obj['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos']);
		$parameters = array(
		  'apikey' => $apiKey,
		  'geocode' => $cord,
		  'kind' => 'metro',
		  'format' => 'json'
		);


        $coords = explode(',', $cord);

        // Меняем местами координаты
        $reversedCoords = $coords[1] . ',' . $coords[0];

        $result['cord'] = $reversedCoords;

        

        $response = file_get_contents('https://geocode-maps.yandex.ru/1.x/?'. http_build_query($parameters));
		$obj = json_decode($response, true);


       // echo $response;

        if (isset($obj['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['name']))
        {
            $metro = $obj['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['name'];

            $result['nearMetro'] = $metro;
        }

        
       
        $result = json_encode(['result' => $result]);

        echo $result;

        }
}