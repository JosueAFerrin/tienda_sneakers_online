<?php
function getConnection() {
    $servername = "localhost";
    $username = "admin";
    $password = "admin";
    $dbname = "tennis_store";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
