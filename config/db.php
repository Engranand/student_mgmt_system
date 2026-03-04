<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'student_mgmt_db');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("<h3 style='color:red'>Database Connection Failed: " . mysqli_connect_error() . "</h3>");
}
?>

<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "student_mgmt_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("<h3 style='color:red'>Connection Failed: " . mysqli_connect_error() . "</h3>");
}
?>