<?php
require '../includes/auth.php';
checkRole('teacher');
require '../config/db.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$branch = isset($_GET['branch']) ? mysqli_real_escape_string($conn, $_GET['branch']) : '';
$year   = isset($_GET['year']) ? $_GET['year'] : '';

$where = "WHERE 1=1";
if ($search) $where .= " AND (u.name LIKE '%$search%' OR s.roll_no LIKE '%$search%')";
if ($branch)  $where .= " AND s.branch = '$branch'";
if ($year)    $where .= " AND s.year = '$year'";

$students = mysqli_query($conn, "
    SELECT s.*, u.name, u.email 
    FROM students s 
    JOIN users u ON s.user_id = u.id 
    $where
    ORDER BY s.roll_no
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students</title>
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
        <a href="view_students.php" class="nav-link active"><i class="fas fa-user-graduate"></i> Students</a>
        <a href="add_marks.php" class="nav-link"><i class="fas fa-marker"></i> Add Marks</a>
        <a href="mark_attendance.php" class="nav-link"><i class="fas fa-calendar-check"></i> Attendance</a>
        <hr>
        <a href="../logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>

<div class="main-content">
    <div class="topbar">
        <h5><i class="fas fa-user-graduate me-2 text-success"></i>View Students</h5>
        <div>
            <i class="fas fa-user-circle me-2 text-success"></i>
            <strong><?= $_SESSION['name'] ?></strong>
            <span class="badge bg-success ms-2">Teacher</span>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control"
                               placeholder="Search by name or roll no..."
                               value="<?= $search ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="branch" class="form-select">
                        <option value="">All Branches</option>
                        <?php foreach(['Computer Science','Information Technology','Electronics','Mechanical','Civil'] as $b): ?>
                        <option value="<?= $b ?>" <?= $branch == $b ? 'selected' : '' ?>><?= $b ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="year" class="form-select">
                        <option value="">All Years</option>
                        <?php for($i=1; $i<=4; $i++): ?>
                        <option value="<?= $i ?>" <?= $year == $i ? 'selected' : '' ?>>Year <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                </div>
            </form>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">All Students</h6>
                <span class="badge bg-success"><?= mysqli_num_rows($students) ?> Students</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Roll No</th>
                            <th>Branch</th>
                            <th>Year</th>
                            <th>Phone</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; while ($s = mysqli_fetch_assoc($students)): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td class="fw-semibold"><?= $s['name'] ?></td>
                            <td><span class="badge bg-light text-dark"><?= $s['roll_no'] ?></span></td>
                            <td><?= $s['branch'] ?></td>
                            <td><span class="badge bg-light text-primary">Year <?= $s['year'] ?></span></td>
                            <td><?= $s['phone'] ?></td>
                            <td><small class="text-muted"><?= $s['email'] ?></small></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if (mysqli_num_rows($students) == 0): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-user-slash fa-2x mb-2 d-block"></i>
                                No students found
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
<script src="/student_mgmt_system/assets/js/main.js"></script>
</body>
</html>