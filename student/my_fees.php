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

$total_paid    = mysqli_fetch_row(mysqli_query($conn, "SELECT COALESCE(SUM(amount),0) FROM fees WHERE student_id=$student_id AND status='Paid'"))[0];
$total_pending = mysqli_fetch_row(mysqli_query($conn, "SELECT COALESCE(SUM(amount),0) FROM fees WHERE student_id=$student_id AND status='Pending'"))[0];
$count_pending = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM fees WHERE student_id=$student_id AND status='Pending'"))[0];

$fees = mysqli_query($conn, "SELECT * FROM fees WHERE student_id=$student_id ORDER BY semester, id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Fees</title>
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
        <a href="my_attendance.php" class="nav-link"><i class="fas fa-calendar-check"></i> My Attendance</a>
        <a href="my_fees.php" class="nav-link active"><i class="fas fa-rupee-sign"></i> My Fees</a>
        <hr>
        <a href="../logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>

<div class="main-content">
    <div class="topbar">
        <h5><i class="fas fa-rupee-sign me-2" style="color:#7b1fa2"></i>My Fees</h5>
        <div>
            <i class="fas fa-user-circle me-2" style="color:#7b1fa2"></i>
            <strong><?= $_SESSION['name'] ?></strong>
            <span class="badge ms-2" style="background:#7b1fa2">Student</span>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="fee-card" style="background: linear-gradient(135deg, #2e7d32, #43a047)">
                <i class="fas fa-check-circle fa-2x mb-2 opacity-75"></i>
                <h2 class="fw-bold">₹<?= number_format($total_paid, 2) ?></h2>
                <p class="mb-0">Total Paid</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="fee-card" style="background: linear-gradient(135deg, #c62828, #e53935)">
                <i class="fas fa-exclamation-circle fa-2x mb-2 opacity-75"></i>
                <h2 class="fw-bold">₹<?= number_format($total_pending, 2) ?></h2>
                <p class="mb-0">Total Pending</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="fee-card" style="background: linear-gradient(135deg, #4a148c, #7b1fa2)">
                <i class="fas fa-file-invoice fa-2x mb-2 opacity-75"></i>
                <h2 class="fw-bold"><?= $count_pending ?></h2>
                <p class="mb-0">Pending Records</p>
            </div>
        </div>
    </div>

    <?php if ($count_pending > 0): ?>
    <div class="alert alert-warning rounded-3 mb-4">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Reminder!</strong> You have <?= $count_pending ?> pending fee(s) of ₹<?= number_format($total_pending, 2) ?>. Please contact admin.
    </div>
    <?php endif; ?>

    <!-- Fees Table -->
    <div class="card">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Fee Records</h6>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Semester</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Paid Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; while ($f = mysqli_fetch_assoc($fees)): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td>Semester <?= $f['semester'] ?></td>
                            <td class="fw-semibold">₹<?= number_format($f['amount'], 2) ?></td>
                            <td>
                                <?php if ($f['status'] == 'Paid'): ?>
                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i>Paid</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="fas fa-clock me-1"></i>Pending</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $f['paid_date'] ? date('d M Y', strtotime($f['paid_date'])) : '-' ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if (mysqli_num_rows($fees) == 0): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-file-invoice fa-2x mb-2 d-block"></i>
                                No fee records found
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