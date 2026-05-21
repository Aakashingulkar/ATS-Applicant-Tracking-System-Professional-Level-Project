<?php

use const Dom\VALIDATION_ERR;
include("config.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != "super_admin"){
    header("Location: login.php");
    exit();
}

if(isset($_POST['register'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $confirm_password = $_POST['confirm_password'];
    $phone = mysqli_real_escape_string($conn,$_POST['phone']);

    if(strlen($password) < 8 ||
       !preg_match('/[A-Z]/',$_POST['password']) ||
       !preg_match('/[a-z]/',$_POST['password']) ||
       !preg_match('/[0-9]/',$_POST['password']) ||
       !preg_match('/[\W]/',$_POST['password'])){
        $error = "Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character!";
    }  
    elseif($_POST['password'] !== $_POST['confirm_password']){
        $error = "Passwords do not match!";
    }
    else {

        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if(mysqli_num_rows($check) > 0){
            $error = "Email already registered!";
        } else {
            $insert = mysqli_query($conn,
                "INSERT INTO users (name, email, password, phone, role, is_verified)
                 VALUES ('$name','$email','$password','$phone','admin',1)"
            );
            if($insert){
                $success = "Admin registered successfully!";
            } else {
                $error = "Registration failed: " . mysqli_error($conn);
            }
        }
    }

}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Register</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background: linear-gradient(to right, #141e30, #243b55);
}
.register-card{
    border-radius:20px;
}
</style>

</head>
<body class="d-flex align-items-center justify-content-center vh-100"
      style="background: url('images/sg.jpg') no-repeat center center/cover;">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top px-4" style="background: rgba(0, 0, 0, 0);">

    <!-- Logo + Brand -->
    <a class="navbar-brand d-flex align-items-center text-dark" href="dashboard.php">
        <img src="images/logo.png" alt="Logo" style="width:40px; height:40px; margin-right:10px;">
        <Strong>Code Craft</Strong>
    </a>

    <div class="ms-auto dropdown">
        <a class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
           href="#"
           role="button"
           data-bs-toggle="dropdown"
           aria-expanded="false">

            <strong><?php echo $_SESSION['name']; ?></strong>
        </a>

        <ul class="dropdown-menu dropdown-menu-end shadow">
            <li>
                <a class="dropdown-item" href="/ats/ATS%20Ganesh/super_admin/profile.php">👤 View Profile</a>
            </li>
            <li>
                <a class="dropdown-item" href="admin_register.php">✏ Create Admin</a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger" href="logout.php">🚪 Logout</a>
            </li>
        </ul>
    </div>

</nav>

<div class="card shadow-lg p-4 text-center" style="width:400px; background: rgba(255, 255, 255, 0); border-radius:10px;">

        <!-- Company Logo -->
        <img src="images/logo.png" alt="Company Logo" style="width:80px; margin:auto; display:block;">
    
        <h3 class="text-center mb-4">Admin Registration</h3>

        <?php if(isset($success)){ ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php } ?>

        <?php if(isset($error)){ ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php } ?>

        <form method="POST">

            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label>Phone Number</label>
                <input type="text" name="phone" class="form-control" required>
            </div>

            <button type="submit" name="register" class="btn btn-dark w-100">
                Register Admin
            </button>
            
            <button class="btn btn-secondary w-100 mt-2" onclick="window.location='/ats/ATS%20Ganesh/super_admin/dashboard.php';">
                Back to Dashboard
            </button>            
        </form>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>