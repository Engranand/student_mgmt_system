<?php
require '../includes/auth.php';
checkRole('student');
require '../config/db.php';

$student = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT s.*, u.name, u.email 
    FROM students s 
    JOIN users u ON s.user_id = u.id 
    WHERE s.user_id = {$_SESSION['user_id']}
"));

$student_id    = $student['id'];
$total_marks   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM marks WHERE student_id=$student_id"))[0];
$fees_pending  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM fees WHERE student_id=$student_id AND status='Pending'"))[0];
$total_present = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM attendance WHERE student_id=$student_id AND status='Present'"))[0];
$total_days    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM attendance WHERE student_id=$student_id"))[0];
$attendance_pct = $total_days > 0 ? round(($total_present / $total_days) * 100) : 0;

$recent_marks = mysqli_query($conn, "SELECT * FROM marks WHERE student_id=$student_id ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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
        <a href="dashboard.php" class="nav-link active"><i class="fas fa-home"></i> Dashboard</a>
        <a href="my_marks.php" class="nav-link"><i class="fas fa-marker"></i> My Marks</a>
        <a href="my_attendance.php" class="nav-link"><i class="fas fa-calendar-check"></i> My Attendance</a>
        <a href="my_fees.php" class="nav-link"><i class="fas fa-rupee-sign"></i> My Fees</a>
        <hr>
        <a href="../logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>

<div class="main-content">
    <div class="topbar">
        <h5><i class="fas fa-home me-2" style="color:#7b1fa2"></i>My Dashboard</h5>
        <div>
            <i class="fas fa-user-circle me-2" style="color:#7b1fa2"></i>
            <strong><?= $_SESSION['name'] ?></strong>
            <span class="badge ms-2" style="background:#7b1fa2">Student</span>
        </div>
    </div>

    <!-- Student Info -->
    <div class="info-card mb-4">
        <div class="avatar">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div>
            <h5 class="fw-bold mb-1"><?= $student['name'] ?></h5>
            <p class="text-muted mb-0">
                <i class="fas fa-id-card me-1"></i><?= $student['roll_no'] ?> &nbsp;|&nbsp;
                <i class="fas fa-building me-1"></i><?= $student['branch'] ?> &nbsp;|&nbsp;
                <i class="fas fa-calendar me-1"></i>Year <?= $student['year'] ?>
            </p>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #1565c0, #1e88e5)">
                <i class="fas fa-marker"></i>
                <h2><?= $total_marks ?></h2>
                <p>Subjects Marked</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #2e7d32, #43a047)">
                <i class="fas fa-calendar-check"></i>
                <h2><?= $attendance_pct ?>%</h2>
                <p>Attendance</p>
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
            <div class="stat-card" style="background: linear-gradient(135deg, #4a148c, #7b1fa2)">
                <i class="fas fa-calendar-day"></i>
                <h2><?= $total_present ?></h2>
                <p>Days Present</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Marks -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="fas fa-marker me-2" style="color:#7b1fa2"></i>Recent Marks</h6>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Marks</th>
                                <th>Semester</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($m = mysqli_fetch_assoc($recent_marks)):
                                $pct = ($m['marks'] / $m['total_marks']) * 100;
                                if ($pct >= 90)     { $grade = 'A+'; $color = 'success'; }
                                elseif ($pct >= 75) { $grade = 'A';  $color = 'primary'; }
                                elseif ($pct >= 60) { $grade = 'B';  $color = 'info'; }
                                elseif ($pct >= 45) { $grade = 'C';  $color = 'warning'; }
                                else                { $grade = 'F';  $color = 'danger'; }
                            ?>
                            <tr>
                                <td class="fw-semibold"><?= $m['subject'] ?></td>
                                <td><?= $m['marks'] ?>/<?= $m['total_marks'] ?></td>
                                <td>Sem <?= $m['semester'] ?></td>
                                <td><span class="badge bg-<?= $color ?>"><?= $grade ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <a href="my_marks.php" class="btn btn-sm btn-outline-primary mt-2">View All Marks</a>
                </div>
            </div>
        </div>

        <!-- Attendance -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="fw-bold mb-3"><i class="fas fa-chart-pie me-2" style="color:#7b1fa2"></i>Attendance</h6>
                    <h1 class="fw-bold" style="font-size:60px;color:#7b1fa2"><?= $attendance_pct ?>%</h1>
                    <p class="text-muted"><?= $total_present ?> Present / <?= $total_days ?> Total Days</p>
                    <div class="progress mb-3">
                        <div class="progress-bar" style="width:<?= $attendance_pct ?>%;background:#7b1fa2"></div>
                    </div>
                    <?php if ($attendance_pct < 75): ?>
                        <div class="alert alert-danger py-2 small mb-3">
                            <i class="fas fa-exclamation-triangle me-1"></i>Below 75%! Attend more classes.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success py-2 small mb-3">
                            <i class="fas fa-check-circle me-1"></i>Good attendance! Keep it up.
                        </div>
                    <?php endif; ?>
                    <a href="my_attendance.php" class="btn btn-sm btn-outline-primary w-100">View Details</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/main.js"></script>
</body>
</html>