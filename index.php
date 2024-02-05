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

            // Send AJAX request to weather.php with the location
            $.ajax({
                type: 'GET',
                url: 'weather.php',
                data: {location: location},
                success: function (xmlData) {
                    // Extract information from XML
                    var locationInfo = xmlData.getElementsByTagName("location")[0].childNodes[0].nodeValue;
                    var temperatureInfo = xmlData.getElementsByTagName("temperature")[0].childNodes[0].nodeValue;

                    // Display information on the webpage
                    var weatherInfoHtml = '<p>Location: ' + locationInfo + '</p>';
                    weatherInfoHtml += '<p>Temperature: ' + temperatureInfo + ' °C</p>';

                    $('#weatherInfo').html(weatherInfoHtml);
                },
                error: function () {
                    // Handle errors here
                    $('#weatherInfo').html('<p>Error fetching weather information.</p>');
                }
            });
        }
    </script>
</div>

<div id="historyUI" style="display: none;">
    <h2>History UI</h2>
    <!-- Add content for the History UI here -->
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
                    console.log(response)
                    // login response
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
            var name = document.getElementById('name').value;
            var email = document.getElementById('email').value;

            // Perform registration logic here
            console.log('Register:', name, email);
        }

        function logoutUser() {
            auth = false;
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
