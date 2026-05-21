<?php
include("config.php");

if(isset($_GET['token'])){

    $token = mysqli_real_escape_string($conn,$_GET['token']);

    $check = mysqli_query($conn,"SELECT * FROM users WHERE verification_token='$token' AND is_verified=0");

    if(mysqli_num_rows($check) > 0){

        mysqli_query($conn,"
        UPDATE users 
        SET is_verified=1, verification_token=NULL 
        WHERE verification_token='$token'
        ");

        echo "<script>
        alert('Email Verified Successfully! You can now login.');
        window.location='login.php';
        </script>";

    } else {
        echo "Invalid or Expired Verification Link!";
    }
}
?>