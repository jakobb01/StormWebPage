<?php
// Database connection details
$servername = "localhost";
$username = "codeigniter";
$password = "codeigniter2019";
$dbname = "PHP_test";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $birthday = mysqli_real_escape_string($conn, $_POST["birthday"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $number = mysqli_real_escape_string($conn, $_POST["number"]);

    // Validate and process the data (you can add your own validation logic here)

    // Insert data into the "User" table
    $sql = "INSERT INTO User (name, birthday, email, number) VALUES ('$name', '$birthday', '$email', '$number')";

    if ($conn->query($sql) === TRUE) {
        echo "Data inserted successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    // If the form is not submitted, redirect to the form page
    header("Location: index.php");
    exit();
}

// Close the database connection
$conn->close();
?>
