<?php
$host     = getenv('MYSQLHOST');
$port     = getenv('MYSQLPORT');
$dbname   = getenv('MYSQLDATABASE');
$username = getenv('MYSQLUSER');
$password = getenv('MYSQLPASSWORD');

$conn = mysqli_connect($host, $username, $password, $dbname, (int)$port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>