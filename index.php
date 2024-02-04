<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather App</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>
<h1>Weather App</h1>

<form id="weatherForm">
    <label for="location">Enter Location:</label>
    <input type="text" id="location" name="location" required>
    <button type="button" onclick="getWeather()">Get Weather</button>
</form>

<div id="weatherInfo">
    <!-- Weather information will be displayed here -->
</div>

<script>
    function getWeather() {
        var location = document.getElementById('location').value;

        // Send AJAX request to weather.php with the location
        $.ajax({
            type: 'GET',
            url: 'weather.php',
            data: { location: location },
            success: function(xmlData) {
                // Extract information from XML
                var locationInfo = xmlData.getElementsByTagName("location")[0].childNodes[0].nodeValue;
                var temperatureInfo = xmlData.getElementsByTagName("temperature")[0].childNodes[0].nodeValue;

                // Display information on the webpage
                var weatherInfoHtml = '<p>Location: ' + locationInfo + '</p>';
                weatherInfoHtml += '<p>Temperature: ' + temperatureInfo + ' °C</p>';

                $('#weatherInfo').html(weatherInfoHtml);
            },
            error: function() {
                // Handle errors here
                $('#weatherInfo').html('<p>Error fetching weather information.</p>');
            }
        });
    }
</script>

</body>
</html>
