<?php
$mysqli = new mysqli("mysql", "myuser", "mypassword", "mydatabase");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$result = $mysqli->query("SELECT * FROM fullname");

echo "<h1>Data from MySQL:</h1>";

if ($result === false) {
    die("Query failed: " . $mysqli->error);
}

while ($row = $result->fetch_assoc()) {
    echo "Name: " . $row['name'] . "<br>";
}

$mysqli->close();
?>
