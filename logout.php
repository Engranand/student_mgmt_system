<?php
session_start();
session_destroy();
header("Location: /student_mgmt_system/login.php");
exit();
?>