<?php

function callAPI($method, $url, $data){
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
        'APIKEY: 111111111111111111111',
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




// Check if the location is provided in the GET request
if (isset($_GET['location'])) {

    $get_data = callAPI('GET', 'https://api.open-meteo.com/v1/forecast?latitude=50.66&longitude=14.04&current=temperature_2m', false);

    $response = json_decode($get_data, true);

    $weatherXml = new SimpleXMLElement('<?xml version="1.0"?><data></data>');

    array_to_xml($response,$weatherXml);



    // Extract relevant information
    $latitude = $weatherXml->latitude;
    $longitude = $weatherXml->longitude;
    $temperature = $weatherXml->current->temperature_2m;

    // Create a new SimpleXMLElement for the response
    $xmlResponse = new SimpleXMLElement('<weather></weather>');

    // Add weather data to the XML document
    $xmlResponse->addChild('location', $weatherXml->latitude . ', ' . $weatherXml->longitude);
    $xmlResponse->addChild('temperature', $weatherXml->current->temperature_2m);


    // Set the content type to XML
    header('Content-Type: application/xml');


    $result = $xmlResponse->asXML('./weather_data/ustinadlabem.xml');

    // Output the XML document
    echo $xmlResponse->asXML();
} else {
    // Location not provided in the GET request
    echo "Please provide a location.";
}




