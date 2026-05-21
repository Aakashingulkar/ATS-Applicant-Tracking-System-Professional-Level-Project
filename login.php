<?php
include("config.php");

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $result = mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($result);

    if($user && password_verify($password,$user['password'])){

    if($user['is_verified'] == 0){
    echo "<script>alert('Please verify your email first!');</script>";
    $redirectUri = "login.php";
    header("Refresh: 0; URL=$redirectUri");
    exit();
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];

    if($user['role']=="super_admin"){
        header("Location: super_admin/dashboard.php");
    }
    elseif($user['role']=="admin"){
        header("Location: admin/dashboard.php");
    }
    else{
        header("Location: candidate/dashboard.php");
    }
    } else {
        $error = "Invalid Email or Password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login - ATS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex align-items-center justify-content-center vh-100"
      style="background: url('images/sg.jpg') no-repeat center center/cover;">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top px-4" style="background: rgba(0, 0, 0, 0);">

    <!-- Logo + Brand -->
    <a class="navbar-brand d-flex align-items-center text-dark" href="index.php">
        <img src="images/logo.png" alt="Logo" style="width:40px; height:40px; margin-right:10px;">
        <strong>Code Craft</strong>
    </a>
    <div class="ms-auto d-flex gap-3">
        <a href="index.php" class="nav-link text-dark"><strong>Home</strong></a>
        <a href="login.php" class="nav-link text-dark"><strong>Career</strong></a>
    </div>
</nav>

<div class="card shadow-lg p-4 text-center"
     style="width:400px; background: rgba(255, 255, 255, 0); border-radius:10px;">

     <!-- Company Logo -->
    <img src="images/logo.png" alt="Company Logo" style="width:80px; margin:auto; display:block;">
    
    <h3 class="text-center mb-4">Login</h3>

    <?php if(isset($error)) { ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php } ?>

    <?php if(isset($_GET['registered'])) { ?>
    <div class="alert alert-success">
        Registration successful! Please login.
    </div>
    <?php } ?>

    <form method="POST">
        <div class="mb-3">
            <label><strong>Email</strong></label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label><strong>Password</strong></label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
    </form>

    <div class="text-center mt-3">
    <a href="forgot_password.php" class="text-decoration-none">
        Forgot Password?
    </a>
    </div>

    <p class="text-center mt-3">
        Don't have an account? <a href="register.php">Register</a>
    </p>
</div>

</body>
</html>