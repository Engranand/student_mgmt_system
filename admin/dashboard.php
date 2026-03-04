<?php
require '../includes/auth.php';
checkRole('admin');
require '../config/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/student_mgmt_system/assets/css/admin.css" rel="stylesheet">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-graduation-cap"></i>
        <h6>Student Mgmt System</h6>
    </div>
    <nav>
        <a href="dashboard.php" class="nav-link active"><i class="fas fa-home"></i> Dashboard</a>
        <a href="manage_students.php" class="nav-link"><i class="fas fa-user-graduate"></i> Students</a>
        <a href="manage_teachers.php" class="nav-link"><i class="fas fa-chalkboard-teacher"></i> Teachers</a>
        <a href="manage_fees.php" class="nav-link"><i class="fas fa-rupee-sign"></i> Fees</a>
        <hr>
        <a href="../logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="topbar">
        <h5><i class="fas fa-home me-2 text-primary"></i>Dashboard</h5>
        <div>
            <i class="fas fa-user-circle me-2 text-primary"></i>
            <strong><?= $_SESSION['name'] ?></strong>
            <span class="badge bg-primary ms-2">Admin</span>
        </div>
    </div>

    <?php
    $total_students = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM students"))[0];
    $total_teachers = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM teachers"))[0];
    $fees_pending   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM fees WHERE status='Pending'"))[0];
    $today          = date('Y-m-d');
    $present_today  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM attendance WHERE date='$today' AND status='Present'"))[0];
    ?>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #1565c0, #1e88e5)">
                <i class="fas fa-user-graduate"></i>
                <h2><?= $total_students ?></h2>
                <p>Total Students</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #2e7d32, #43a047)">
                <i class="fas fa-chalkboard-teacher"></i>
                <h2><?= $total_teachers ?></h2>
                <p>Total Teachers</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #e65100, #fb8c00)">
                <i class="fas fa-rupee-sign"></i>
                <h2><?= $fees_pending ?></h2>
                <p>Fees Pending</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #6a1b9a, #ab47bc)">
                <i class="fas fa-calendar-check"></i>
                <h2><?= $present_today ?></h2>
                <p>Present Today</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Quick Actions</h6>
            <a href="manage_students.php" class="btn btn-primary me-2">
                <i class="fas fa-user-plus me-1"></i> Add Student
            </a>
            <a href="manage_teachers.php" class="btn btn-success me-2">
                <i class="fas fa-plus me-1"></i> Add Teacher
            </a>
            <a href="manage_fees.php" class="btn btn-warning">
                <i class="fas fa-rupee-sign me-1"></i> Manage Fees
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/student_mgmt_system/assets/js/main.js"></script>
</body>
</html>