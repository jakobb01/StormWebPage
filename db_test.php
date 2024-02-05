<?php

$db = new SQLite3('php.db');


// INSERT
// $db->exec("INSERT INTO users(name, email) VALUES('Test123', 'test@test.com')");

// SELECT ALL
//$res = $db->query('SELECT * FROM users');

//while ($row = $res->fetchArray()) {
//    echo "{$row['id']} {$row['name']} {$row['email']} \n";
//}

// DELETE
//$db->exec("DELETE FROM users WHERE name='Test123' ");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $postdata = file_get_contents("php://input");
    $xml = simplexml_load_string($postdata);

    if ($xml !== false) {
        if ($xml->getName() === "loginRequest") {

            $name = $xml->name;
            $email = $xml->email;

            // TODO: "better" check credentials against the database
            $result = $db->query("SELECT email FROM users WHERE name='$name'");

            $row = $result->fetchArray();

            if ($row["email"] == $email) {
                // user found
                echo "1";
            } else {
                // failed login
                echo "2";
            }

        } elseif ($xml->getName() === "registerRequest") {
            $name = $xml->name;
            $email = $xml->email;

            $sql = "INSERT INTO users (name, email) VALUES ('$name', '$email')";
            if ($db->query($sql)) {
                // success
                echo "1";
            } else {
                // registration failed
                echo "2";
            }
        } elseif ($xml->getName() === "saveWeather") {
            $name = $xml->userName;
            $location = $xml->location;
            $date = $xml->date;
            $weather = $xml->weather;
            $maxTemp = $xml->maxTemp;
            $minTemp = $xml->minTemp;
            $sunrise = $xml->sunrise;
            $sunset = $xml->sunset;

            // TODO: get user id from db with his name

            // TODO: store weather data with user id to the database

            echo $name, $location, $date, $weather, $maxTemp, $minTemp, $sunrise, $sunset;

        } else {
            echo "Invalid XML request";
        }
    } else {
        echo "Invalid XML data";
    }
}