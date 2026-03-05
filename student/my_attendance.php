<?php
require '../includes/auth.php';
checkRole('student');
require '../config/db.php';

$student = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT s.*, u.name FROM students s 
    JOIN users u ON s.user_id = u.id 
    WHERE s.user_id = {$_SESSION['user_id']}
"));
$student_id = $student['id'];

$total_present = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM attendance WHERE student_id=$student_id AND status='Present'"))[0];
$total_absent  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM attendance WHERE student_id=$student_id AND status='Absent'"))[0];
$total_days    = $total_present + $total_absent;
$pct           = $total_days > 0 ? round(($total_present / $total_days) * 100) : 0;

$attendance = mysqli_query($conn, "SELECT * FROM attendance WHERE student_id=$student_id ORDER BY date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/student.css" rel="stylesheet">
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-user-graduate"></i>
        <h6>Student Panel</h6>
    </div>
    <nav>
        <a href="dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a>
        <a href="my_marks.php" class="nav-link"><i class="fas fa-marker"></i> My Marks</a>
        <a href="my_attendance.php" class="nav-link active"><i class="fas fa-calendar-check"></i> My Attendance</a>
        <a href="my_fees.php" class="nav-link"><i class="fas fa-rupee-sign"></i> My Fees</a>
        <hr>
        <a href="../logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>

<div class="main-content">
    <div class="topbar">
        <h5><i class="fas fa-calendar-check me-2" style="color:#7b1fa2"></i>My Attendance</h5>
        <div>
            <i class="fas fa-user-circle me-2" style="color:#7b1fa2"></i>
            <strong><?= $_SESSION['name'] ?></strong>
            <span class="badge ms-2" style="background:#7b1fa2">Student</span>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card text-center p-4">
                <i class="fas fa-calendar-check fa-2x text-success mb-2"></i>
                <h2 class="fw-bold text-success"><?= $total_present ?></h2>
                <p class="text-muted mb-0">Days Present</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-4">
                <i class="fas fa-calendar-times fa-2x text-danger mb-2"></i>
                <h2 class="fw-bold text-danger"><?= $total_absent ?></h2>
                <p class="text-muted mb-0">Days Absent</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-4">
                <i class="fas fa-calendar fa-2x text-primary mb-2"></i>
                <h2 class="fw-bold text-primary"><?= $total_days ?></h2>
                <p class="text-muted mb-0">Total Days</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-4">
                <i class="fas fa-percent fa-2x mb-2" style="color:#7b1fa2"></i>
                <h2 class="fw-bold" style="color:#7b1fa2"><?= $pct ?>%</h2>
                <p class="text-muted mb-0">Attendance %</p>
            </div>
        </div>
    </div>

    <!-- Progress -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <span class="fw-semibold">Overall Attendance</span>
                <span class="fw-bold"><?= $pct ?>%</span>
            </div>
            <div class="progress mb-3">
                <div class="progress-bar <?= $pct >= 75 ? 'bg-success' : 'bg-danger' ?>"
                     style="width:<?= $pct ?>%"></div>
            </div>
            <?php if ($pct < 75): ?>
            <div class="alert alert-danger mb-0">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Warning!</strong> Attendance below 75%. You may not be allowed in exams.
            </div>
            <?php else: ?>
            <div class="alert alert-success mb-0">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Good!</strong> Attendance above 75%. Keep it up!
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Records -->
    <div class="card">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Attendance Records</h6>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; while ($a = mysqli_fetch_assoc($attendance)): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= date('d M Y', strtotime($a['date'])) ?></td>
                            <td><?= date('l', strtotime($a['date'])) ?></td>
                            <td>
                                <?php if ($a['status'] == 'Present'): ?>
                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i>Present</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Absent</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if ($total_days == 0): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="fas fa-calendar-times fa-2x mb-2 d-block"></i>
                                No attendance records yet
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/main.js"></script>
</body>
</html>