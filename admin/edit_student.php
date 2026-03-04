<?php
require '../includes/auth.php';
checkRole('admin');
require '../config/db.php';

// ID check karo
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_students.php");
    exit();
}

$id = (int)$_GET['id'];