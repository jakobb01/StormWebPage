<?php

$db = new SQLite3('php.db');


// INSERT
// $db->exec("INSERT INTO users(name, email) VALUES('Test123', 'test@test.com')");

// SELECT ALL
$res = $db->query('SELECT * FROM users');

while ($row = $res->fetchArray()) {
    echo "{$row['id']} {$row['name']} {$row['email']} \n";
}

// DELETE
//$db->exec("DELETE FROM users WHERE name='Test123' ");

