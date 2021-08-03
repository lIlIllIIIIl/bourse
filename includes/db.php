<?php

$ip = "localhost";
$port = 3306;
$username = "root";
$password = "";
$dbname = "bourse";

$pdo = new PDO("mysql:host=localhost;dbname=bourse", $username, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
])

?>