<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather App</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>

<script>
    let auth = false;
    let name = "";
</script>


<h1>Weather App</h1>

<nav>
    <button onclick="showSearchUI()">SEARCH</button>
    <button onclick="showHistoryUI()">HISTORY</button>
    <button onclick="showLoginUI()">LOGIN</button>
</nav>

<div id="searchUI" style="display: none;">
    <br>
    <br>
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
            document.getElementById('weatherForm').reset();

            // Send AJAX request to weather.php with the location
            $.ajax({
                type: 'GET',
                url: 'weather.php',
                data: {location: location},
                success: function (xmlData) {
                    // Extract information from XML
                    //var locationInfo = xmlData.getElementsByTagName("location")[0].childNodes[0].nodeValue;
                    var weathers = xmlData.getElementsByTagName("weather");
                    var temperatureMaxs = xmlData.getElementsByTagName("temperatureMax");
                    var temperatureMins = xmlData.getElementsByTagName("temperatureMin");
                    var sunrises = xmlData.getElementsByTagName("sunrise");
                    var sunsets = xmlData.getElementsByTagName("sunset");

                    console.log(weathers)

                    var today = new Date();
                    var todayDateString = today.toISOString().split("T")[0];


                    // Create HTML to display the information
                    var html = "<h3>Weather Information for " + location + "</h3>";
                    html += "<ul>";

                    for (var i = 0; i < weathers.length; i++) {
                        var weather = weathers[i].childNodes[0].nodeValue;
                        var temperatureMax = temperatureMaxs[i].childNodes[0].nodeValue;
                        var temperatureMin = temperatureMins[i].childNodes[0].nodeValue;
                        var sunrise = new Date(sunrises[i].childNodes[0].nodeValue).toISOString().split("T");
                        var sunset = new Date(sunsets[i].childNodes[0].nodeValue).toISOString().split("T");
                        sunrise[1] = sunrise[1].split(".")[0];
                        sunset[1] = sunset[1].split(".")[0];

                        html += "<br>";
                        html += "<div>";
                        html += "<strong>";

                        if (sunrise[0] === todayDateString) {
                            html += "TODAY: ";
                        } else {
                            html += new Date(sunrise[0]).toLocaleDateString() + ": ";
                        }


                        html += "</strong>" + weather + "<br>";
                        html += "<strong>Max Temperature:</strong> " + temperatureMax + "°C<br>";
                        html += "<strong>Min Temperature:</strong> " + temperatureMin + "°C<br>";
                        html += "<strong>Sunrise:</strong> " + sunrise[1] + "<br>";
                        html += "<strong>Sunset:</strong> " + sunset[1];
                        html += "</div>";
                        html += "<br>";

                        // Add button with onclick event to save weather data
                        html += "<button onclick=\"saveWeather('" + location + "','" + sunrise[0] + "','" + weather + "','" + temperatureMax + "','" + temperatureMin + "','" + sunrise[1] + "','" + sunset[1] + "')\">Save Weather Day " + new Date(sunrise[0]).toLocaleDateString() + "</button>";
                    }

                    html += "</ul>";

                    $('#weatherInfo').html(html);
                },
                error: function () {
                    // Handle errors here
                    $('#weatherInfo').html('<p>Error fetching weather information.</p>');
                }
            });
        }

        function saveWeather(location, date, weather, maxTemp, minTemp, sunrise, sunset) {
            if (name === "") {
                alert("Please login to use this feature!");
                return false
            }
            var xmlData = '<saveWeather>';
            xmlData += '<userName>' + name + '</userName>';
            xmlData += '<location>' + location + '</location>';
            xmlData += '<date>' + date + '</date>';
            xmlData += '<weather>' + weather + '</weather>';
            xmlData += '<maxTemp>' + maxTemp + '</maxTemp>';
            xmlData += '<minTemp>' + minTemp + '</minTemp>';
            xmlData += '<sunrise>' + sunrise + '</sunrise>';
            xmlData += '<sunset>' + sunset + '</sunset>';
            xmlData += '</saveWeather>';

            // post req to db_test.php to store all info into database
            $.ajax({
                type: 'POST',
                url: 'db_test.php',
                contentType: 'application/xml',
                data: xmlData,
                success: function (response) {
                    console.log('Success:', response);
                    // You can add further actions here if needed
                },
                error: function (error) {
                    console.error('Error:', error);
                }
            });
        }
    </script>
</div>

<div id="historyUI" style="display: none;">
    <script>
        function userHistory() {
            if (auth) {
                // call database and show it here, all places + temperature a user has saved to date
                var historyHtml = '<h3>' + name + ' history: </h3>';
                $('#historyUI').html(historyHtml);
            } else {
                var noHistoryHtml = '<h3>Login to use this feature!</h3>';
                $('#historyUI').html(noHistoryHtml);
            }
        }
    </script>
</div>

<div id="authLoginUI" style="display: none;">
    <!-- Displayed if correctly signed in or not. -->
</div>

<div id="loginUI" style="display: none;">
    <h2>Login or Register</h2>

    <form id="loginForm">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <br>
        <br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <br>

        <button type="button" onclick="login()">Login</button>
        <button type="button" onclick="register()">Register</button>
    </form>
    <script>
        function login() {
            var uname = document.getElementById('name').value;
            var email = document.getElementById('email').value;
            document.getElementById('loginForm').reset();

            // Create XML data
            var xmlData = '<loginRequest>';
            xmlData += '<name>' + uname + '</name>';
            xmlData += '<email>' + email + '</email>';
            xmlData += '</loginRequest>';

            // POST req to db_test.php
            $.ajax({
                type: 'POST',
                url: 'db_test.php',
                contentType: 'application/xml',
                data: xmlData,
                success: function(response) {
                    if (response === "1") {
                        auth = true;
                        name = uname;
                        userLoggedInUI(name)
                    }
                    else {
                        showAuthLoginUI();
                        var incorrectLoginHtml = '<h3>Wrong username or email, please try again!</h3>'
                        $('#authLoginUI').html(incorrectLoginHtml);
                    }
                },
                error: function() {
                    // handle errors here
                    console.log('Error during login request');
                }
            });
        }

        function register() {
            var uname = document.getElementById('name').value;
            var email = document.getElementById('email').value;
            document.getElementById('loginForm').reset();

            // Create XML data
            var xmlData = '<registerRequest>';
            xmlData += '<name>' + uname + '</name>';
            xmlData += '<email>' + email + '</email>';
            xmlData += '</registerRequest>';

            // POST req to db_test.php - registerRequest
            $.ajax({
                type: 'POST',
                url: 'db_test.php',
                contentType: 'application/xml',
                data: xmlData,
                success: function (response) {
                    if (response === "1") {
                        showAuthLoginUI();
                        var correctRegHtml = '<h3>Successful registration. You can now login!</h3>'
                        $('#authLoginUI').html(correctRegHtml);

                    } else {
                        showAuthLoginUI();
                        var incorrectRegHtml = '<h3>Something went wrong... please try again later</h3>'
                        $('#authLoginUI').html(incorrectRegHtml);
                    }
                },
                error: function () {
                    // handle errors here
                    console.log('Error during registration');
                }
            });
        }

        function logoutUser() {
            auth = false;
            name = "";
            showLoginUI();

        }

        function userLoggedInUI(name) {
            showAuthLoginUI();
            var successfulLoginHtml = '<h3>Successfully logged in!</h3>'
            successfulLoginHtml += '<p>Welcome back, ' + name + '</p>';
            successfulLoginHtml += '<button onClick="logoutUser()">LOGOUT</button>'
            $('#authLoginUI').html(successfulLoginHtml);
        }
    </script>
</div>

<script>
    function showSearchUI() {
        hideAllUIs();
        $('#searchUI').show();
    }

    function showHistoryUI() {
        hideAllUIs();
        userHistory()
        $('#historyUI').show();
    }

    function showLoginUI() {
        hideAllUIs();
        if (!auth) {
            $('#loginUI').show();
        } else {
            userLoggedInUI(name)
        }

    }

    function showAuthLoginUI() {
        hideAllUIs();
        $('#authLoginUI').show();
    }

    function hideAllUIs() {
        $('#searchUI, #historyUI, #loginUI, #authLoginUI').hide();
    }
</script>

</body>
</html>
