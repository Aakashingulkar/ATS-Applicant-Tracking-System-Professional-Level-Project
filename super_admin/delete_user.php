<?php
include("../config.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != "super_admin"){
    die("Access Denied!");
}

$id = $_GET['id'];

// Prevent deleting super_admin
$check = mysqli_query($conn,"SELECT role FROM users WHERE id='$id'");
$data = mysqli_fetch_assoc($check);

if($data['role'] == 'super_admin'){
    die("You cannot delete Super Admin!");
}

mysqli_query($conn,"UPDATE users SET is_deleted=1 WHERE id='$id'");
mysqli_query($conn,"UPDATE users SET is_verified=0 WHERE id='$id'");

header("Location: dashboard.php");
?>