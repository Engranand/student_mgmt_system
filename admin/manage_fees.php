<?php
require '../includes/auth.php';
checkRole('admin');
require '../config/db.php';

// Add Fee
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_fee'])) {
    $student_id = $_POST['student_id'];
    $amount     = $_POST['amount'];
    $semester   = $_POST['semester'];
    $status     = $_POST['status'];
    $paid_date  = $status == 'Paid' ? $_POST['paid_date'] : NULL;

    if ($paid_date) {
        $q = "INSERT INTO fees (student_id, amount, paid_date, status, semester) VALUES ('$student_id', '$amount', '$paid_date', '$status', '$semester')";
    } else {
        $q = "INSERT INTO fees (student_id, amount, status, semester) VALUES ('$student_id', '$amount', '$status', '$semester')";
    }
    mysqli_query($conn, $q);
    $success = "Fee record added successfully!";
}

// Delete Fee
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM fees WHERE id=$id");
    header("Location: manage_fees.php");
    exit();
}

// Mark Paid
if (isset($_GET['mark_paid'])) {
    $id   = $_GET['mark_paid'];
    $date = date('Y-m-d');
    mysqli_query($conn, "UPDATE fees SET status='Paid', paid_date='$date' WHERE id=$id");
    header("Location: manage_fees.php");
    exit();
}

$students_list = mysqli_query($conn, "SELECT s.id, u.name, s.roll_no FROM students s JOIN users u ON s.user_id = u.id");
$fees = mysqli_query($conn, "
    SELECT f.*, u.name, s.roll_no 
    FROM fees f 
    JOIN students s ON f.student_id = s.id 
    JOIN users u ON s.user_id = u.id 
    ORDER BY f.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Fees</title>
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
        <a href="manage_students.php" class="nav-link"><i class="fas fa-user-graduate"></i> Students</a>
        <a href="manage_teachers.php" class="nav-link"><i class="fas fa-chalkboard-teacher"></i> Teachers</a>
        <a href="manage_fees.php" class="nav-link active"><i class="fas fa-rupee-sign"></i> Fees</a>
        <hr>
        <a href="../logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>

<div class="main-content">
    <div class="topbar">
        <h5><i class="fas fa-rupee-sign me-2 text-primary"></i>Manage Fees</h5>
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

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="fas fa-plus me-2 text-primary"></i>Add Fee Record</h6>
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
                            <label class="form-label">Amount (₹)</label>
                            <input type="number" name="amount" class="form-control" placeholder="e.g. 25000" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Semester</label>
                            <select name="semester" class="form-select" required>
                                <?php for($i=1; $i<=8; $i++): ?>
                                <option value="<?= $i ?>">Semester <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" id="statusSelect" required>
                                <option value="Pending">Pending</option>
                                <option value="Paid">Paid</option>
                            </select>
                        </div>
                        <div class="mb-3" id="paidDateDiv" style="display:none">
                            <label class="form-label">Paid Date</label>
                            <input type="date" name="paid_date" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        <button type="submit" name="add_fee" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-2"></i>Add Fee Record
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0"><i class="fas fa-list me-2 text-primary"></i>All Fee Records</h6>
                        <span class="badge bg-warning text-dark"><?= mysqli_num_rows($fees) ?> Records</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Semester</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Paid Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($f = mysqli_fetch_assoc($fees)): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?= $f['name'] ?></div>
                                        <small class="text-muted"><?= $f['roll_no'] ?></small>
                                    </td>
                                    <td>Sem <?= $f['semester'] ?></td>
                                    <td>₹<?= number_format($f['amount'], 2) ?></td>
                                    <td>
                                        <?php if ($f['status'] == 'Paid'): ?>
                                            <span class="badge bg-success">Paid</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $f['paid_date'] ?? '-' ?></td>
                                    <td>
                                        <?php if ($f['status'] == 'Pending'): ?>
                                        <a href="?mark_paid=<?= $f['id'] ?>" class="btn btn-sm btn-outline-success me-1"
                                           onclick="return confirm('Mark as paid?')">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <?php endif; ?>
                                        <a href="?delete=<?= $f['id'] ?>" class="btn btn-sm btn-outline-danger"
                                           onclick="return confirmDelete()">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if (mysqli_num_rows($fees) == 0): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-file-invoice fa-2x mb-2 d-block"></i>
                                        No fee records yet
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
<script>
    document.getElementById('statusSelect').addEventListener('change', function() {
        document.getElementById('paidDateDiv').style.display =
            this.value == 'Paid' ? 'block' : 'none';
    });
</script>
</body>
</html>