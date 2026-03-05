<?php
require '../includes/auth.php';
checkRole('teacher');
require '../config/db.php';

$teacher = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT t.*, u.name, u.email 
    FROM teachers t 
    JOIN users u ON t.user_id = u.id 
    WHERE t.user_id = {$_SESSION['user_id']}
"));

$total_students = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM students"))[0];
$today          = date('Y-m-d');
$present_today  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM attendance WHERE date='$today' AND status='Present'"))[0];
$total_marks    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM marks WHERE added_by={$_SESSION['user_id']}"))[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/teacher.css" rel="stylesheet">
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-chalkboard-teacher"></i>
        <h6>Teacher Panel</h6>
    </div>
    <nav>
        <a href="dashboard.php" class="nav-link active"><i class="fas fa-home"></i> Dashboard</a>
        <a href="view_students.php" class="nav-link"><i class="fas fa-user-graduate"></i> Students</a>
        <a href="add_marks.php" class="nav-link"><i class="fas fa-marker"></i> Add Marks</a>
        <a href="mark_attendance.php" class="nav-link"><i class="fas fa-calendar-check"></i> Attendance</a>
        <hr>
        <a href="../logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>

<div class="main-content">
    <div class="topbar">
        <h5><i class="fas fa-home me-2 text-success"></i>Teacher Dashboard</h5>
        <div>
            <i class="fas fa-user-circle me-2 text-success"></i>
            <strong><?= $_SESSION['name'] ?></strong>
            <span class="badge bg-success ms-2">Teacher</span>
        </div>
    </div>

    <!-- Teacher Info -->
    <div class="card mb-4">
        <div class="card-body d-flex align-items-center">
            <div class="rounded-circle p-3 me-3" style="background:#2e7d32">
                <i class="fas fa-user-tie fa-2x text-white"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-1"><?= $_SESSION['name'] ?></h5>
                <p class="text-muted mb-0">
                    <i class="fas fa-building me-1"></i><?= $teacher['department'] ?> &nbsp;|&nbsp;
                    <i class="fas fa-book me-1"></i><?= $teacher['subject'] ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #1565c0, #1e88e5)">
                <i class="fas fa-user-graduate"></i>
                <h2><?= $total_students ?></h2>
                <p>Total Students</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #2e7d32, #43a047)">
                <i class="fas fa-calendar-check"></i>
                <h2><?= $present_today ?></h2>
                <p>Present Today</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #e65100, #fb8c00)">
                <i class="fas fa-marker"></i>
                <h2><?= $total_marks ?></h2>
                <p>Marks Added</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Quick Actions</h6>
            <a href="mark_attendance.php" class="btn btn-success me-2">
                <i class="fas fa-calendar-check me-1"></i> Mark Attendance
            </a>
            <a href="add_marks.php" class="btn btn-warning me-2">
                <i class="fas fa-marker me-1"></i> Add Marks
            </a>
            <a href="view_students.php" class="btn btn-primary">
                <i class="fas fa-users me-1"></i> View Students
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/main.js"></script>
</body>
</html>