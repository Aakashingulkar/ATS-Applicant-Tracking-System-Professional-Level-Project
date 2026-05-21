<?php
include("../config.php");
include("../free_ai_screening.php");
require '../vendor/autoload.php';

use Smalot\PdfParser\Parser;

error_reporting(E_ALL);
ini_set('display_errors', 1);

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != "candidate"){
    header("Location: ../login.php");
    exit();
}

/* Validate job_id */
if(!isset($_GET['job_id'])){
    die("Invalid Job Access!");
}

$job_id = intval($_GET['job_id']);
$user_id = $_SESSION['user_id'];

$user_query = mysqli_query($conn, "SELECT name FROM users WHERE id='$user_id'");
$user_data = mysqli_fetch_assoc($user_query);
$candidate_name = $user_data['name'] ?? 'Candidate';
$already_applied = false;

/* Fetch Job (only active jobs) */
$job_query = mysqli_query($conn,
    "SELECT * FROM jobs WHERE id='$job_id' AND is_deleted=0"
);

if(mysqli_num_rows($job_query) == 0){
    die("Job not found or deleted!");
}

$job = mysqli_fetch_assoc($job_query);

/* Check duplicate application */
$check = mysqli_query($conn,
    "SELECT id FROM applications 
     WHERE job_id='$job_id' 
     AND candidate_id='$user_id'"
);

if(mysqli_num_rows($check) > 0){
    $already_applied = true;

}

$success = "";
$error = "";

/* Handle Form Submission */
if(isset($_POST['apply'])){

    $phone = trim($_POST['phone']);
    $experience = intval($_POST['experience']);
    $preferred_location = trim($_POST['preferred_location']);

    /* Phone Validation */
    if(!preg_match('/^[0-9]{10}$/', $phone)){
        $error = "Phone number must be 10 digits!";
    }
    elseif($_FILES['resume']['error'] != 0){
        $error = "Resume upload failed!";
    }
    else{

        /* Create uploads folder if not exists */
        if(!is_dir("../uploads")){
            mkdir("../uploads", 0777, true);
        }

        $resume_name = time() . "_" . basename($_FILES['resume']['name']);
        $upload_path = "../uploads/" . $resume_name;

        if(move_uploaded_file($_FILES['resume']['tmp_name'], $upload_path)){

            // ============================
            // ✅ RESUME TEXT EXTRACTION FIX
            // ============================

            $resume_text = "";

            $file_ext = pathinfo($resume_name, PATHINFO_EXTENSION);

            if(strtolower($file_ext) == "pdf"){
                try{
                    $parser = new Parser();
                    $pdf = $parser->parseFile($upload_path);
                    $resume_text = $pdf->getText();
                } catch(Exception $e){
                    $resume_text = "PDF parsing failed";
                }
            } else {
                $resume_text = file_get_contents($upload_path);
            }

            // Safety fallback
            if(empty($resume_text)){
                $resume_text = "No content";
            }

            // ============================
            // ✅ FREE AI SCREENING
            // ============================

            $result = freeAIScreening($resume_text, $job['description']);

            $score = $result['score'];
            $status = $result['status'];
            $output = $result['remark'];

            $insert = mysqli_query($conn,"INSERT INTO applications
            (job_id, candidate_id, resume, phone, experience, preferred_location, status, screening_score, remark)
            VALUES
            ('$job_id', '$user_id', '$resume_name', '$phone', '$experience', '$preferred_location', '$status', '$score', '$output')");

            if($insert){
                $success = "Application Submitted Successfully! Status: $status";
            }else{
                $error = "Database Error: " . mysqli_error($conn);
            }

            }else{
                $error = "Failed to upload resume!";
            }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Apply Job</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: linear-gradient(135deg, hsl(182, 80%, 90%), #69439b); min-height:100vh;">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">

    <!-- Logo + Brand -->
    <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
        <img src="../images/logo.png" alt="Logo" style="width:40px; height:40px; margin-right:10px;">
        Code Craft
    </a>

    <div class="ms-auto dropdown">
        <a class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
           href="#"
           role="button"
           data-bs-toggle="dropdown"
           aria-expanded="true">

            <?php echo $candidate_name; ?>
        </a>

        <ul class="dropdown-menu dropdown-menu-end shadow">
            <li>
                <a class="dropdown-item" href="profile.php">View Profile</a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger" href="../logout.php">Logout</a>
            </li>
        </ul>
        </div>
</nav>

<div class="container mt-5">
<div class="card shadow p-4">

<h4>Apply for: <?php echo $job['title']; ?></h4>
<p><strong>Job Category:</strong> <?php echo $job['category']; ?></p>
<p><strong>Job Location:</strong> <?php echo $job['location']; ?></p>
<p><strong>Required Experience:</strong> <?php echo $job['required_experience']; ?> Years</p>
<p><strong>Job Description:</strong><?php echo $job['description']; ?></p>

<hr>

<?php if($error != ""){ ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php } ?>

<?php if($success != ""){ ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php } ?>

<form method="POST" enctype="multipart/form-data">

<h5>Phone Number</h5>

<div class="mb-3">
    <input type="text" name="phone" class="form-control" required>
</div>

<h5>Experience(Years)</h5>

<div class="mb-3">
    <input type="number" name="experience" class="form-control" required>
</div>

<h5>Preferred Location</h5>

<div class="mb-3">
    <input type="text" name="preferred_location" class="form-control" required>
</div>

<h5>Upload Resume</h5>

<div class="mb-3">
    <input type="file" name="resume" class="form-control" required>
</div>

<button type="submit" name="apply" class="btn btn-primary" onclick="window.location.href='dashboard.php'">
    Submit Application
</button>

<button type="button" class="btn btn-secondary" onclick="window.location.href='dashboard.php'">
    Back to Dashboard
</button>

</form>

</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>