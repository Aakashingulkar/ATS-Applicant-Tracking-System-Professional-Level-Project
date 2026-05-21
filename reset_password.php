<?php
include("config.php");

$error = "";
$success = "";

if(isset($_GET['token'])){
    $token = mysqli_real_escape_string($conn,$_GET['token']);
} else {
    die("Invalid Access");
}

if(isset($_POST['password'])){

    $password_raw = $_POST['password'];

    // Strong password validation
    if(strlen($password_raw) < 8 ||
       !preg_match('/[A-Z]/',$password_raw) ||
       !preg_match('/[0-9]/',$password_raw) ||
       !preg_match('/[\W]/',$password_raw)){

        $error = "Password must contain:
        - Minimum 8 characters
        - 1 uppercase letter
        - 1 number
        - 1 special character";

    } else {

        $password = password_hash($password_raw,PASSWORD_DEFAULT);

        mysqli_query($conn,"UPDATE users 
                            SET password='$password', reset_token=NULL 
                            WHERE reset_token='$token'");

        $success = "Password updated successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
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
    
    <h4 class="text-center mb-4">Reset Password</h4>

        <?php if($error){ ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

        <?php if($success){ ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
                <br><br>
                <a href="login.php" class="btn btn-success w-100">Go to Login</a>
            </div>
        <?php } ?>

        <?php if(!$success){ ?>
        <form method="POST">
            <div class="mb-3">
                <label>New Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Update Password
            </button>
        </form>
        <?php } ?>

    </div>
</div>

</body>
</html>