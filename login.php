<?php
require 'config/db.php';
require 'includes/auth.php';

if (isset($_SESSION['user_id'])) {
    header("Location: " . $_SESSION['role'] . "/dashboard.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login    = mysqli_real_escape_string($conn, $_POST['login']);
    $password = $_POST['password'];

    $query  = "SELECT * FROM users WHERE email = '$login'";
    $result = mysqli_query($conn, $query);
    $user   = mysqli_fetch_assoc($result);

    if (!$user) {
        $query  = "SELECT u.* FROM users u 
                   JOIN students s ON s.user_id = u.id 
                   WHERE s.roll_no = '$login'";
        $result = mysqli_query($conn, $query);
        $user   = mysqli_fetch_assoc($result);
    }

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];

        header("Location: " . $user['role'] . "/dashboard.php");
        exit();
    } else {
        $error = "Invalid credentials!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/student_mgmt_system/assets/css/login.css" rel="stylesheet">
</head>
<body>
    <body>
<div class="bg-animation">
    <span></span><span></span><span></span><span></span><span></span>
</div>
<div class="grid-lines"></div>
<div class="particles" id="particles"></div>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <div class="icon-wrap">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h4>Student Management System</h4>
                <p>Sign in to your account</p>
            </div>
            <div class="login-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger mb-3">
                        <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email / Roll Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="login" class="form-control"
                                   placeholder="Enter email or roll number" required autofocus>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password" id="passInput"
                                   class="form-control" placeholder="Enter your password" required>
                            <button class="btn toggle-pass" type="button" id="togglePass">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-login w-100 text-white">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                </form>
            </div>
        </div>
    </div>
    
<script>

// Particles generate karo
const container = document.getElementById('particles');
for (let i = 0; i < 40; i++) {
    const p = document.createElement('div');
    p.classList.add('particle');
    p.style.left = Math.random() * 100 + '%';
    p.style.animationDuration = (Math.random() * 10 + 8) + 's';
    p.style.animationDelay = (Math.random() * 10) + 's';
    p.style.width = p.style.height = (Math.random() * 4 + 1) + 'px';
    container.appendChild(p);
}
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/student_mgmt_system/assets/js/main.js"></script>
</body>
</html>