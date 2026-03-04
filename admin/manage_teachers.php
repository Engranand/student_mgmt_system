<?php
require '../includes/auth.php';
checkRole('admin');
require '../config/db.php';

// Add Teacher
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_teacher'])) {
    $name       = mysqli_real_escape_string($conn, $_POST['name']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $subject    = mysqli_real_escape_string($conn, $_POST['subject']);
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $q1 = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', 'teacher')";
    if (mysqli_query($conn, $q1)) {
        $user_id = mysqli_insert_id($conn);
        $q2 = "INSERT INTO teachers (user_id, department, subject) VALUES ('$user_id', '$department', '$subject')";
        mysqli_query($conn, $q2);
        $success = "Teacher added successfully!";
    } else {
        $error = "Error: Email already exists!";
    }
}

// Delete Teacher
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $user_id_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT user_id FROM teachers WHERE id=$id"));
    mysqli_query($conn, "DELETE FROM users WHERE id=" . $user_id_row['user_id']);
    header("Location: manage_teachers.php");
    exit();
}

$teachers = mysqli_query($conn, "
    SELECT t.*, u.name, u.email 
    FROM teachers t 
    JOIN users u ON t.user_id = u.id 
    ORDER BY t.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/student_mgmt_system/assets/css/admin.css" rel="stylesheet">
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-graduation-cap"></i>
        <h6>Student Mgmt System</h6>
    </div>
    <nav>
        <a href="dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a>
        <a href="manage_students.php" class="nav-link"><i class="fas fa-user-graduate"></i> Students</a>
        <a href="manage_teachers.php" class="nav-link active"><i class="fas fa-chalkboard-teacher"></i> Teachers</a>
        <a href="manage_fees.php" class="nav-link"><i class="fas fa-rupee-sign"></i> Fees</a>
        <hr>
        <a href="../logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>

<div class="main-content">
    <div class="topbar">
        <h5><i class="fas fa-chalkboard-teacher me-2 text-primary"></i>Manage Teachers</h5>
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
                    <h6 class="fw-bold mb-3"><i class="fas fa-plus me-2 text-primary"></i>Add New Teacher</h6>
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
                            <label class="form-label">Department</label>
                            <select name="department" class="form-select" required>
                                <option value="">Select Department</option>
                                <option>Computer Science</option>
                                <option>Information Technology</option>
                                <option>Electronics</option>
                                <option>Mechanical</option>
                                <option>Civil</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" placeholder="e.g. Data Structures" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Set password" required>
                        </div>
                        <button type="submit" name="add_teacher" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>Add Teacher
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0"><i class="fas fa-list me-2 text-primary"></i>All Teachers</h6>
                        <span class="badge bg-success"><?= mysqli_num_rows($teachers) ?> Teachers</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Subject</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($t = mysqli_fetch_assoc($teachers)): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?= $t['name'] ?></div>
                                        <small class="text-muted"><?= $t['email'] ?></small>
                                    </td>
                                    <td><?= $t['department'] ?></td>
                                    <td><span class="badge bg-light text-dark"><?= $t['subject'] ?></span></td>
                                    <td>
                                        <a href="?delete=<?= $t['id'] ?>" class="btn btn-sm btn-outline-danger"
                                           onclick="return confirmDelete()">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if (mysqli_num_rows($teachers) == 0): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-user-slash fa-2x mb-2 d-block"></i>
                                        No teachers added yet
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