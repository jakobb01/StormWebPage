<?php

function callAPI($method, $url, $data, $key){
    $curl = curl_init();
    switch ($method){
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }
    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'X-Api-Key:' . $key,
        'Content-Type: application/json',
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    // EXECUTE:
    $result = curl_exec($curl);
    if(!$result){die("Connection Failure");}
    curl_close($curl);
    return $result;
}

function array_to_xml( $data, &$xml_data ) {
    foreach( $data as $key => $value ) {
        if( is_array($value) ) {
            if( is_numeric($key) ){
                $key = 'item'.$key; //dealing with <0/>..<n/> issues
            }
            $subnode = $xml_data->addChild($key);
            array_to_xml($value, $subnode);
        } else {
            $xml_data->addChild("$key",htmlspecialchars("$value"));
        }
    }
}

function getCoordinates( $city ) {
    $key = file_get_contents('key');
    return callAPI('GET', 'https://api.api-ninjas.com/v1/geocoding?city=' . $city, false, $key);
}


// TODO: Check if the location is provided in the GET request
if (isset($_GET['location'])) {
    $location = urlencode($_GET['location']);
    $coordinates_json = getCoordinates($location);
    $coordinates = json_decode($coordinates_json, true);
    $lat = $coordinates[0]['latitude'];
    $lon = $coordinates[0]['longitude'];

    $xmlResponse = build_weather($lat, $lon, $location);

    // Set the content type to XML
    header('Content-Type: application/xml');

    $result = $xmlResponse->asXML('./weather_data/' . $location . '.xml');

    echo $xmlResponse->asXML();
}

function build_weather($lat, $lon, $location)
    {
        $wmo = [0, 1, 2, 3, 45, 48, 51, 53, 55, 56, 57, 61, 63, 65, 66, 67, 71, 73, 75, 77, 80, 81, 82, 85, 86, 95, 96, 99];
        $wmo_name = ["clear sky", "mainly clear", "partly cloudy", "overcast", "fog", "depositing rime fog", "light drizzle", "moderate drizzle", "dense drizzle", "slight rain", "moderate rain", "heavy rain", "freezing rain: light", "freezing rain: heavy", "slight snow fall", "moderate snow fall", "heavy snow fall", "snow grains", "slight rain showers", "moderate rain shower", "violent rain shower", "slight snow shower", "heavy snow shower", "thunderstorm", "thunderstorm", "thunderstorm"];


        $get_data = callAPI('GET', 'https://api.open-meteo.com/v1/forecast?latitude='. $lat .'&longitude='. $lon .'&daily=weather_code,temperature_2m_max,temperature_2m_min,sunrise,sunset', false, '');


        $response = json_decode($get_data, true);

        $weatherXml = new SimpleXMLElement('<?xml version="1.0"?><data></data>');

        array_to_xml($response, $weatherXml);

        // Extract weather information
        $latitude = $weatherXml->latitude;
        $longitude = $weatherXml->longitude;
        $temperature_max = $weatherXml->daily->temperature_2m_max;
        $temperature_min = $weatherXml->daily->temperature_2m_min;
        $sunrise = $weatherXml->daily->sunrise;
        $sunset = $weatherXml->daily->sunset;

        // extract weather code -> convert to strings
        $weather_code = $weatherXml->daily->weather_code;
        // Convert SimpleXMLElement objects to an array
        $weather_code_array = [];
        for ($i=0; $i < 7; $i++) {
            $weather_code_array[$i] = $weather_code->{$i};
        }
        // Map weather codes to corresponding names
        $weather_strings = [];
        foreach ($weather_code_array as $code) {
            $index = array_search($code, $wmo); // Find the index of the code in the wmo array
            if ($index !== false && isset($wmo_name[$index])) {
                $weather_strings[] = $wmo_name[$index];
            } else {
                // Handle cases where the code is not found in the mapping
                $weather_strings[] = "Unknown"; // Or any default value you prefer
            }
        }
        // weather_string ARRAY READY FOR XML


        // Create a new SimpleXMLElement for the response
        $xmlResponse = new SimpleXMLElement('<weather_data></weather_data>');

        // Add location information
        $xmlResponse->addChild('location', $latitude . ', ' . $longitude);

        childToXml($xmlResponse, $weather_strings, 'weather', false);
        childToXml($xmlResponse, $temperature_max, 'temperatureMax', true);
        childToXml($xmlResponse, $temperature_min, 'temperatureMin', true);
        childToXml($xmlResponse, $sunrise, 'sunrise', true);
        childToXml($xmlResponse, $sunset, 'sunset', true);

        // Output the XML document
        return $xmlResponse;
    }

function childToXml($xmlResponse, $attribute, $attributeName, $howtoaccess)
{
    // Create a "temperatures" child element to hold the daily temperature data
    $temperaturesElement = $xmlResponse->addChild($attributeName.'s');

    // Add temperatures for each day
    for ($day = 0; $day <= 6; $day++) {
        // Replace this with the actual temperature data for each day
        if ($howtoaccess) {
            $temperatureForDay = $attribute->{$day};
        } else {
            $temperatureForDay = $attribute[$day];
        }


        // Add the temperature to the "temperatures" element
        $temperaturesElement->addChild($attributeName, $temperatureForDay);
    }
    return $xmlResponse;
}
