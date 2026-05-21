<?php
include("config.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

if(isset($_POST['register'])){

    $name = mysqli_real_escape_string($conn,$_POST['name']);
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $password_raw = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = mysqli_real_escape_string($conn,$_POST['phone']);

    // ✅ Password Validation
    if(strlen($password_raw) < 8 ||
       !preg_match('/[A-Z]/',$password_raw) ||
       !preg_match('/[a-z]/',$password_raw) ||
       !preg_match('/[0-9]/',$password_raw) ||
       !preg_match('/[\W]/',$password_raw)){
        $error = "Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character!";
    }
    elseif($password_raw !== $confirm_password){
        $error = "Passwords do not match!";
        
    } elseif(!preg_match('/^\d{10}$/', $phone)){
        $error = "Phone number must be 10 digits!";
    }
    else {

        $password = password_hash($password_raw, PASSWORD_DEFAULT);

        $check = mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");

        if(mysqli_num_rows($check) > 0){
            $error = "Email already exists!";
        } 
        else {

            $token = bin2hex(random_bytes(32));

            mysqli_query($conn,"
            INSERT INTO users (name, email, password, role, phone, is_verified, verification_token)
            VALUES ('$name', '$email', '$password', 'candidate', '$phone', 0, '$token')
            ");

            $verify_link = "http://localhost/ats/ATS%20Ganesh/verify.php?token=$token";

            $mail = new PHPMailer(true);

            try {

                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = //'your mail id';
                $mail->Password   = //'App Password';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
                $mail->Port       = 587;

                $mail->setFrom(/*'your mail id',*/ 'CodeCraft');
                $mail->addAddress($email, $name);

                $mail->isHTML(true);
                $mail->Subject = "Verify Your Email - CodeCraft";

                $mail->Body = "
                <h3>Hello $name,</h3>
                <p>Thank you for registering with CodeCraft.</p>
                <p>Please click the button below to verify your email address:</p>

                <p>
                <a href='$verify_link' style='
                    background-color:#28a745;
                    color:white;
                    padding:10px 20px;
                    text-decoration:none;
                    border-radius:5px;'>
                    Verify Email
                </a>
                </p>

                <br>
                <p>If you did not create this account, please ignore this email.</p>
                <p>Regards,<br>CodeCraft Team</p>
                ";

                $mail->SMTPDebug = 0;
                $mail->send();

                $success = "Registration successful! Please check your email to verify your account.";

            } catch (Exception $e) {
                $error = "Registration successful but Email could not be sent.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Register - ATS</title>
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
    
    <h3 class="text-center mb-4">Create Account</h3>

    <?php if(isset($success)) { ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php } ?>

    <?php if(isset($error)) { ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php } ?>

    <form method="POST">

        <div class="mb-3">
            <label>Full Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Phone Number</label>
            <input type="text" name="phone" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        
        <button type="submit" name="register" class="btn btn-success w-100">
            Register
        </button>
    </form>

    <p class="text-center mt-3">
        Already have an account? <a href="login.php">Login</a>
    </p>
</div>

</body>
</html>