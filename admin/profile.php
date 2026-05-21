<?php
include("../config.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];
$result = mysqli_query($conn,"SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
<title>My Profile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background: linear-gradient(to right, #667eea, #764ba2);
}

.profile-card{
    border-radius:20px;
}

.profile-title{
    color:#5a189a;
    font-weight:bold;
}

.profile-info{
    font-size:16px;
    padding:8px 0;
}
</style>

</head>
<body style="background: linear-gradient(135deg, hsl(182, 80%, 90%), #69439b); min-height:100vh;">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">

    <!-- Logo + Brand -->
    <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
        <img src="../images/logo.png" alt="Logo" style="width:40px; height:40px; margin-right:10px;">
        Code Craft
    </a>
    
    <button class="btn btn-dark">
        <i class="bi bi-list"></i>
    </button>

    <div class="ms-auto dropdown">
        <a class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
           href="#"
           role="button"
           data-bs-toggle="dropdown"
           aria-expanded="false">

            <?php echo $_SESSION['name']; ?>
        </a>

        <ul class="dropdown-menu dropdown-menu-end shadow">
            <li>
                <a class="dropdown-item" href="profile.php">👤 View Profile</a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger" href="../logout.php">🚪 Logout</a>
            </li>
        </ul>
    </div>

</nav>
<div class="container mt-5">

    <div class="card shadow-lg p-4" style="max-width:600px; background: rgba(255, 255, 255, 0);margin:auto;">
        
        <h3 class="mb-4 text-center profile-title">My Profile</h3>

        <hr>

        <div class="profile-info">
            <strong>👤 Name:</strong> 
            <?php echo $user['name'] ?? 'Not Available'; ?>
        </div>

        <div class="profile-info">
            <strong>📧 Email:</strong> 
            <?php echo $user['email'] ?? 'Not Available'; ?>
        </div>

        <div class="profile-info">
            <strong>📱 Phone:</strong> 
            <?php echo $user['phone'] ?? 'Not Updated'; ?>
        </div>

        <hr>

        <div class="text-center mt-3">
            <a href="dashboard.php" class="btn btn-dark px-4">Back</a>
        </div>

    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>