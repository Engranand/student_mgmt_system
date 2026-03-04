<?php
require '../includes/auth.php';
checkRole('teacher');
require '../config/db.php';

// Add Marks
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_marks'])) {
    $student_id = $_POST['student_id'];
    $subject    = mysqli_real_escape_string($conn, $_POST['subject']);
    $marks      = $_POST['marks'];
    $total      = $_POST['total_marks'];
    $semester   = $_POST['semester'];
    $added_by   = $_SESSION['user_id'];

    $q = "INSERT INTO marks (student_id, subject, marks, total_marks, semester, added_by) 
          VALUES ('$student_id', '$subject', '$marks', '$total', '$semester', '$added_by')";
    mysqli_query($conn, $q);
    $success = "Marks added successfully!";
}

// Delete Marks
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM marks WHERE id=$id");
    header("Location: add_marks.php");
    exit();
}

$students_list = mysqli_query($conn, "SELECT s.id, u.name, s.roll_no FROM students s JOIN users u ON s.user_id = u.id");
$marks_list    = mysqli_query($conn, "
    SELECT m.*, u.name, s.roll_no 
    FROM marks m 
    JOIN students s ON m.student_id = s.id 
    JOIN users u ON s.user_id = u.id 
    ORDER BY m.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Marks</title>
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
        <a href="add_marks.php" class="nav-link active"><i class="fas fa-marker"></i> Add Marks</a>
        <a href="mark_attendance.php" class="nav-link"><i class="fas fa-calendar-check"></i> Attendance</a>
        <hr>
        <a href="../logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>

<div class="main-content">
    <div class="topbar">
        <h5><i class="fas fa-marker me-2 text-success"></i>Add Marks</h5>
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
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="fas fa-plus me-2 text-success"></i>Add New Marks</h6>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Select Student</label>
                            <select name="student_id" class="form-select" required>
                                <option value="">Select Student</option>
                                <?php while ($s = mysqli_fetch_assoc($students_list)): ?>
                                <option value="<?= $s['id'] ?>"><?= $s['name'] ?> (<?= $s['roll_no'] ?>)</option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" placeholder="e.g. Data Structures" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Marks Obtained</label>
                            <input type="number" name="marks" class="form-control" placeholder="e.g. 85" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Marks</label>
                            <input type="number" name="total_marks" class="form-control" value="100" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Semester</label>
                            <select name="semester" class="form-select" required>
                                <?php for($i=1; $i<=8; $i++): ?>
                                <option value="<?= $i ?>">Semester <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <button type="submit" name="add_marks" class="btn btn-success w-100">
                            <i class="fas fa-plus me-2"></i>Add Marks
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="fas fa-list me-2 text-success"></i>All Marks Records</h6>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Subject</th>
                                    <th>Marks</th>
                                    <th>Semester</th>
                                    <th>Grade</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($m = mysqli_fetch_assoc($marks_list)):
                                    $pct = ($m['marks'] / $m['total_marks']) * 100;
                                    if ($pct >= 90)     { $grade = 'A+'; $color = 'success'; }
                                    elseif ($pct >= 75) { $grade = 'A';  $color = 'primary'; }
                                    elseif ($pct >= 60) { $grade = 'B';  $color = 'info'; }
                                    elseif ($pct >= 45) { $grade = 'C';  $color = 'warning'; }
                                    else                { $grade = 'F';  $color = 'danger'; }
                                ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?= $m['name'] ?></div>
                                        <small class="text-muted"><?= $m['roll_no'] ?></small>
                                    </td>
                                    <td><?= $m['subject'] ?></td>
                                    <td><?= $m['marks'] ?>/<?= $m['total_marks'] ?></td>
                                    <td>Sem <?= $m['semester'] ?></td>
                                    <td><span class="badge bg-<?= $color ?>"><?= $grade ?> (<?= round($pct) ?>%)</span></td>
                                    <td>
                                        <a href="?delete=<?= $m['id'] ?>" class="btn btn-sm btn-outline-danger"
                                           onclick="return confirmDelete()">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if (mysqli_num_rows($marks_list) == 0): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-marker fa-2x mb-2 d-block"></i>
                                        No marks added yet
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/student_mgmt_system/assets/js/main.js"></script>
</body>
</html>