<?php
$host     = 'sql303.infinityfree.com';
$dbname   = 'if0_41311920_studentmgmt';
$username = 'if0_41311920';
$password = 'pi9WVsqvaJplM';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>