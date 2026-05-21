<?php
include("../config.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../login.php");
    exit();
}

if(isset($_GET['id'])){

    $job_id = intval($_GET['id']);

    mysqli_query($conn,
        "UPDATE jobs SET is_deleted = 0 WHERE id='$job_id'"
    );

    header("Location: deleted_jobs.php?msg=Job Restored");
}
?>