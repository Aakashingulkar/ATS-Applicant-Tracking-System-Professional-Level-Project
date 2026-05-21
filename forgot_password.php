<?php
include("config.php");

$success = "";
$error = "";

if(isset($_POST['email'])){

    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $token = bin2hex(random_bytes(16));

    $check = mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");

    if(mysqli_num_rows($check) > 0){

        mysqli_query($conn,"UPDATE users SET reset_token='$token' WHERE email='$email'");

        $reset_link = "http://localhost/ats/ATS%20Ganesh/reset_password.php?token=$token";

        $success = "Click below to reset password:";
    } else {
        $error = "Email not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center vh-100"
        style="background: url('images/sg.jpg') no-repeat center center/cover;">

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
    
    <h4 class="text-center mb-4">Forget Pasword</h4>

        <?php if($error){ ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

        <?php if($success){ ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
                <br><br>
                <a href="<?php echo $reset_link; ?>" class="btn btn-success w-100">
                    Reset Password
                </a>
            </div>
        <?php } ?>

        <form method="POST">
            <div class="mb-3">
                <label>Enter Registered Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Send Reset Link
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="login.php">Back to Login</a>
        </div>

    </div>
</div>

</body>
</html>