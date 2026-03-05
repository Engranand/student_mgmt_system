<?php
require '../includes/auth.php';
checkRole('admin');
require '../config/db.php';

// Add Student
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_student'])) {
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $roll_no  = mysqli_real_escape_string($conn, $_POST['roll_no']);
    $branch   = mysqli_real_escape_string($conn, $_POST['branch']);
    $year     = $_POST['year'];
    $phone    = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $q1 = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', 'student')";
    if (mysqli_query($conn, $q1)) {
        $user_id = mysqli_insert_id($conn);
        $q2 = "INSERT INTO students (user_id, roll_no, branch, year, phone) VALUES ('$user_id', '$roll_no', '$branch', '$year', '$phone')";
        mysqli_query($conn, $q2);
        $success = "Student added successfully!";
    } else {
        $error = "Error: Email already exists!";
    }
}

// Delete Student
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $user_id_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT user_id FROM students WHERE id=$id"));
    mysqli_query($conn, "DELETE FROM users WHERE id=" . $user_id_row['user_id']);
    header("Location: manage_students.php");
    exit();
}

// Fetch all students
$students = mysqli_query($conn, "
    SELECT s.*, u.name, u.email 
    FROM students s 
    JOIN users u ON s.user_id = u.id 
    ORDER BY s.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/admin.css" rel="stylesheet">
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-graduation-cap"></i>
        <h6>Student Mgmt System</h6>
    </div>
    <nav>
        <a href="dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a>
        <a href="manage_students.php" class="nav-link active"><i class="fas fa-user-graduate"></i> Students</a>
        <a href="manage_teachers.php" class="nav-link"><i class="fas fa-chalkboard-teacher"></i> Teachers</a>
        <a href="manage_fees.php" class="nav-link"><i class="fas fa-rupee-sign"></i> Fees</a>
        <hr>
        <a href="../logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>

<div class="main-content">
    <div class="topbar">
        <h5><i class="fas fa-user-graduate me-2 text-primary"></i>Manage Students</h5>
        <div>
            <i class="fas fa-user-circle me-2 text-primary"></i>
            <strong><?= $_SESSION['name'] ?></strong>
            <span class="badge bg-primary ms-2">Admin</span>
        </div>
    </div>

    <?php if (isset($success)): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="fas fa-user-plus me-2 text-primary"></i>Add New Student</h6>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Enter email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Roll Number</label>
                            <input type="text" name="roll_no" class="form-control" placeholder="e.g. CS2024001" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Branch</label>
                            <select name="branch" class="form-select" required>
                                <option value="">Select Branch</option>
                                <option>Computer Science</option>
                                <option>Information Technology</option>
                                <option>Electronics</option>
                                <option>Mechanical</option>
                                <option>Civil</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Year</label>
                            <select name="year" class="form-select" required>
                                <option value="">Select Year</option>
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" placeholder="Enter phone" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Set password" required>
                        </div>
                        <button type="submit" name="add_student" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>Add Student
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0"><i class="fas fa-list me-2 text-primary"></i>All Students</h6>
                        <span class="badge bg-primary"><?= mysqli_num_rows($students) ?> Students</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Roll No</th>
                                    <th>Branch</th>
                                    <th>Year</th>
                                    <th>Phone</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($s = mysqli_fetch_assoc($students)): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?= $s['name'] ?></div>
                                        <small class="text-muted"><?= $s['email'] ?></small>
                                    </td>
                                    <td><span class="badge bg-light text-dark"><?= $s['roll_no'] ?></span></td>
                                    <td><?= $s['branch'] ?></td>
                                    <td><span class="badge-year">Year <?= $s['year'] ?></span></td>
                                    <td><?= $s['phone'] ?></td>
                                    <td>
                                        <a href="edit_student.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?delete=<?= $s['id'] ?>" class="btn btn-sm btn-outline-danger"
                                           onclick="return confirmDelete()">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if (mysqli_num_rows($students) == 0): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-user-slash fa-2x mb-2 d-block"></i>
                                        No students added yet
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
<script src="/assets/js/main.js"></script>
</body>
</html>