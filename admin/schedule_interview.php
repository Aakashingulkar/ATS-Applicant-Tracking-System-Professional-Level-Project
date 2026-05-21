<?php
include("../config.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../src/Exception.php';
require '../src/PHPMailer.php';
require '../src/SMTP.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../login.php");
    exit();
}

if(!isset($_GET['id'])){
    die("Invalid Access");
}

$app_id = intval($_GET['id']);

$query = mysqli_query($conn,"
SELECT a.*, u.email, u.name, j.title
FROM applications a
JOIN users u ON a.candidate_id = u.id
JOIN jobs j ON a.job_id = j.id
WHERE a.id='$app_id'
");

$data = mysqli_fetch_assoc($query);

if(isset($_POST['schedule'])){

    $interview_link = mysqli_real_escape_string($conn, $_POST['interview_link']);
    $interview_date = mysqli_real_escape_string($conn, $_POST['interview_date']);
    $formatted_date = date("l, d F Y \\a\\t h:i A", strtotime($interview_date));

    
    // Update database
    $update = mysqli_query($conn,"
    UPDATE applications 
    SET interview_link='$interview_link',
        interview_date='$interview_date',
        status='Interview Scheduled'
    WHERE id='$app_id'
    ");

    if(!$update){
        die("Update Failed: " . mysqli_error($conn));
    }

    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = //'your mail id';  
        $mail->Password   = //'App Password';  // 🔴 Your 16 digit App Password (NO SPACES)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom(/*'your mail id',*/ 'CodeCraft Team');
        $mail->addAddress($data['email'], $data['name']);

        $mail->isHTML(true);
        $mail->Subject = "Interview Scheduled for {$data['title']} Position";

        $mail->Body = "
        <h3>Dear {$data['name']},</h3>

        <p>Congratulations! 🎉</p>

        <p>
        We are pleased to inform you that you have been 
        <strong>selected for the position of {$data['title']}</strong>.
        </p>

        <p>Your interview has been successfully scheduled as per the details below:</p>

        <p><strong>Job Title:</strong> {$data['title']}</p>
        <p><strong>Date & Time:</strong> $formatted_date</p>

        <p><strong>Interview Link:</strong><br>
        <a href='$interview_link'>$interview_link</a></p>

        <br>

        <p>Please make sure to join the interview on time.</p>

        <p><strong>Best Regards,<br>
        CodeCraft Team</strong></p>";

        $mail->send();

        echo "<script>
        alert('Interview Scheduled & Email Sent Successfully!');
        window.location='view_applications.php';
        </script>";

    } catch (Exception $e) {

        echo "<script>
        alert('Email Failed: {$mail->ErrorInfo}');
        window.location='view_applications.php';
        </script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Schedule Interview</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
<div class="card p-4 shadow">

<h4>Schedule Interview for <?php echo $data['name']; ?></h4>

<form method="POST">

<div class="mb-3">
<label>Interview Date & Time</label>
<input type="datetime-local" name="interview_date" class="form-control" required>
</div>

<div class="mb-3">
<label>Interview Link (Zoom/Meet)</label>
<input type="text" name="interview_link" class="form-control" required>
</div>

<button type="submit" name="schedule" class="btn btn-primary">
Schedule Interview
</button>

<a href="view_applications.php" class="btn btn-secondary">Back</a>

</form>

</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>