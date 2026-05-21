<?php
include("../config.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../login.php");
    exit();
}

if(isset($_GET['id'])){

    $job_id = intval($_GET['id']);

    $update = mysqli_query($conn,
        "UPDATE jobs SET is_deleted = 1 WHERE id='$job_id'"
    );

    header("Location: dashboard.php?msg=Job Deleted Successfully");
}
?>