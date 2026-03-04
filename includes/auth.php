<?php
session_start();

function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /student_mgmt_system/login.php");
        exit();
    }
}

function checkRole($required_role) {
    checkLogin();
    if ($_SESSION['role'] !== $required_role) {
        header("Location: /student_mgmt_system/login.php");
        exit();
    }
}
?>