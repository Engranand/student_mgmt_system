<?php
require '../includes/auth.php';
checkRole('teacher');
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mark_attendance'])) {
    $date      = $_POST['date'];
    $students  = mysqli_query($conn, "SELECT id FROM students");
    $marked_by = $_SESSION['user_id'];

    while ($s = mysqli_fetch_assoc($students)) {
        $student_id = $s['id'];
        $status     = isset($_POST['attendance'][$student_id]) ? 'Present' : 'Absent';

        $check = mysqli_fetch_row(mysqli_query($conn, "SELECT id FROM attendance WHERE student_id=$student_id AND date='$date'"));
        if ($check) {
            mysqli_query($conn, "UPDATE attendance SET status='$status' WHERE student_id=$student_id AND date='$date'");
        } else {
            mysqli_query($conn, "INSERT INTO attendance (student_id, date, status, marked_by) VALUES ('$student_id', '$date', '$status', '$marked_by')");
        }
    }
    $success = "Attendance marked for $date!";
}

$today    = date('Y-m-d');
$date     = isset($_POST['date']) ? $_POST['date'] : $today;

$students = mysqli_query($conn, "
    SELECT s.id, u.name, s.roll_no, s.branch, s.year,
           a.status as att_status
    FROM students s 
    JOIN users u ON s.user_id = u.id
    LEFT JOIN attendance a ON s.id = a.student_id AND a.date = '$date'
    ORDER BY s.roll_no
");

$report = mysqli_query($conn, "
    SELECT a.date, 
           SUM(a.status='Present') as present,
           SUM(a.status='Absent') as absent
    FROM attendance a 
    GROUP BY a.date 
    ORDER BY a.date DESC 
    LIMIT 7
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/student_mgmt_system/assets/css/teacher.css" rel="stylesheet">
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-chalkboard-teacher"></i>
        <h6>Teacher Panel</h6>
    </div>
    <nav>
        <a href="dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a>
        <a href="view_students.php" class="nav-link"><i class="fas fa-user-graduate"></i> Students</a>
        <a href="add_marks.php" class="nav-link"><i class="fas fa-marker"></i> Add Marks</a>
        <a href="mark_attendance.php" class="nav-link active"><i class="fas fa-calendar-check"></i> Attendance</a>
        <hr>
        <a href="../logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>

<div class="main-content">
    <div class="topbar">
        <h5><i class="fas fa-calendar-check me-2 text-success"></i>Mark Attendance</h5>
        <div>
            <i class="fas fa-user-circle me-2 text-success"></i>
            <strong><?= $_SESSION['name'] ?></strong>
            <span class="badge bg-success ms-2">Teacher</span>
        </div>
    </div>

    <?php if (isset($success)): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="fw-bold mb-0">Student Attendance</h6>
                            <input type="date" name="date" class="form-control w-auto"
                                   value="<?= $date ?>" max="<?= $today ?>"
                                   onchange="this.form.submit()">
                        </div>

                        <div class="mb-3 p-3 bg-light rounded-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label fw-semibold" for="selectAll">
                                    Select All Present
                                </label>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Roll No</th>
                                        <th>Name</th>
                                        <th>Branch</th>
                                        <th>Year</th>
                                        <th class="text-center">Present</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 0;
                                    while ($s = mysqli_fetch_assoc($students)):
                                        $count++;
                                        $is_present = $s['att_status'] == 'Present';
                                    ?>
                                    <tr>
                                        <td><span class="badge bg-light text-dark"><?= $s['roll_no'] ?></span></td>
                                        <td class="fw-semibold"><?= $s['name'] ?></td>
                                        <td><?= $s['branch'] ?></td>
                                        <td>Year <?= $s['year'] ?></td>
                                        <td class="text-center">
                                            <input class="form-check-input att-checkbox"
                                                   type="checkbox"
                                                   name="attendance[<?= $s['id'] ?>]"
                                                   value="1"
                                                   <?= $is_present ? 'checked' : '' ?>>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if ($count == 0): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-user-slash fa-2x mb-2 d-block"></i>
                                            No students found
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($count > 0): ?>
                        <button type="submit" name="mark_attendance" class="btn btn-success w-100 mt-2">
                            <i class="fas fa-save me-2"></i>Save Attendance
                        </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="fas fa-chart-bar me-2 text-success"></i>Last 7 Days</h6>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th class="text-success">Present</th>
                                <th class="text-danger">Absent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($r = mysqli_fetch_assoc($report)): ?>
                            <tr>
                                <td><?= date('d M', strtotime($r['date'])) ?></td>
                                <td><span class="badge bg-success"><?= $r['present'] ?></span></td>
                                <td><span class="badge bg-danger"><?= $r['absent'] ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/student_mgmt_system/assets/js/main.js"></script>
<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.att-checkbox').forEach(cb => cb.checked = this.checked);
    });
</script>
</body>
</html>