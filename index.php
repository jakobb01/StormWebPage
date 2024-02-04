<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Form</title>
</head>
<body>

<?php
// Load the form structure from form.xml
$formXml = simplexml_load_file('form.xml');

// Generate HTML form based on the XML content
echo '<form action="process.php" method="post">';
foreach ($formXml->input as $input) {
    $name = $input['name'];
    $label = $input['label'];
    $type = $input['type'];

    echo "<label for=\"$name\">$label:</label>";
    echo "<input type=\"$type\" name=\"$name\" id=\"$name\"><br>";
}
echo '<input type="submit" value="Submit">';
echo '</form>';
?>

</body>
</html>
<?php
