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
$student_id = $student['id'];

$sem   = isset($_GET['semester']) ? $_GET['semester'] : '';
$where = $sem ? "AND m.semester = '$sem'" : '';

$marks = mysqli_query($conn, "
    SELECT m.* FROM marks m 
    WHERE m.student_id = $student_id $where 
    ORDER BY m.semester, m.subject
");

$all_marks = mysqli_query($conn, "SELECT marks, total_marks FROM marks WHERE student_id=$student_id");
$total_pct = 0; $count = 0;
while ($r = mysqli_fetch_assoc($all_marks)) {
    $total_pct += ($r['marks'] / $r['total_marks']) * 100;
    $count++;
}
$avg  = $count > 0 ? round($total_pct / $count, 2) : 0;
$cgpa = round($avg / 9.5, 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Marks</title>
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
        <a href="my_marks.php" class="nav-link active"><i class="fas fa-marker"></i> My Marks</a>
        <a href="my_attendance.php" class="nav-link"><i class="fas fa-calendar-check"></i> My Attendance</a>
        <a href="my_fees.php" class="nav-link"><i class="fas fa-rupee-sign"></i> My Fees</a>
        <hr>
        <a href="../logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>

<div class="main-content">
    <div class="topbar">
        <h5><i class="fas fa-marker me-2" style="color:#7b1fa2"></i>My Marks</h5>
        <div>
            <i class="fas fa-user-circle me-2" style="color:#7b1fa2"></i>
            <strong><?= $_SESSION['name'] ?></strong>
            <span class="badge ms-2" style="background:#7b1fa2">Student</span>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="cgpa-box">
                <i class="fas fa-star fa-2x mb-2 opacity-75"></i>
                <h1 class="fw-bold"><?= $cgpa ?></h1>
                <p class="mb-1">CGPA</p>
                <small class="opacity-75"><?= $avg ?>% Average</small>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="row w-100 text-center">
                        <div class="col-4">
                            <h3 class="fw-bold text-primary"><?= $count ?></h3>
                            <p class="text-muted mb-0">Total Subjects</p>
                        </div>
                        <div class="col-4">
                            <h3 class="fw-bold text-success"><?= $avg ?>%</h3>
                            <p class="text-muted mb-0">Average Score</p>
                        </div>
                        <div class="col-4">
                            <h3 class="fw-bold" style="color:#7b1fa2"><?= $student['branch'] ?></h3>
                            <p class="text-muted mb-0">Branch</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body p-3">
            <form method="GET" class="d-flex align-items-center gap-3">
                <label class="fw-semibold mb-0">Filter by Semester:</label>
                <select name="semester" class="form-select w-auto" onchange="this.form.submit()">
                    <option value="">All Semesters</option>
                    <?php for($i=1; $i<=8; $i++): ?>
                    <option value="<?= $i ?>" <?= $sem == $i ? 'selected' : '' ?>>Semester <?= $i ?></option>
                    <?php endfor; ?>
                </select>
                <?php if ($sem): ?>
                <a href="my_marks.php" class="btn btn-outline-secondary btn-sm">Clear</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Marks Table -->
    <div class="card">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Marks Details</h6>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Subject</th>
                            <th>Marks</th>
                            <th>Percentage</th>
                            <th>Semester</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; while ($m = mysqli_fetch_assoc($marks)):
                            $pct = round(($m['marks'] / $m['total_marks']) * 100, 1);
                            if ($pct >= 90)     { $grade = 'A+'; $color = 'success'; }
                            elseif ($pct >= 75) { $grade = 'A';  $color = 'primary'; }
                            elseif ($pct >= 60) { $grade = 'B';  $color = 'info'; }
                            elseif ($pct >= 45) { $grade = 'C';  $color = 'warning'; }
                            else                { $grade = 'F';  $color = 'danger'; }
                        ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td class="fw-semibold"><?= $m['subject'] ?></td>
                            <td><?= $m['marks'] ?>/<?= $m['total_marks'] ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:8px">
                                        <div class="progress-bar bg-<?= $color ?>" style="width:<?= $pct ?>%"></div>
                                    </div>
                                    <small><?= $pct ?>%</small>
                                </div>
                            </td>
                            <td>Sem <?= $m['semester'] ?></td>
                            <td><span class="badge bg-<?= $color ?>"><?= $grade ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if (mysqli_num_rows($marks) == 0): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-marker fa-2x mb-2 d-block"></i>
                                No marks found
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