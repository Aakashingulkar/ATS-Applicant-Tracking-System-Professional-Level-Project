<?php
include("../config.php");

//for progress tracker
function getStepIndex($status){
    $steps = ["Applied", "Shortlisted", "Interview Scheduled", "Selected"];
    return array_search($status, $steps);
}


if(!isset($_SESSION['role']) || $_SESSION['role'] != "candidate"){
    header("Location: ../login.php");
    exit();
}


$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn,"SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($result);
$_SESSION['name'] = $user['name'];


/* Dashboard Statistics */
$total_jobs = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM jobs 
     WHERE status='Open' AND deadline >= CURDATE()"))['total'];

$total_applied = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM applications 
     WHERE candidate_id='$user_id'"))['total'];



$result = mysqli_query($conn,"
    SELECT * FROM jobs 
    WHERE status='Open' 
    AND deadline >= CURDATE()
    AND is_deleted=0
    ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Candidate Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.card{
    border-radius:15px;
    transition: 0.3s ease;
}

.card:hover{
    transform: scale(1.02);
}

.job-title{
    color:#00796b;
}

.badge-status{
    font-size:14px;
}
</style>

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

<div class="row mb-4">
    <div class="col-md-6">
        <h2>Welcome, <?php echo $_SESSION['name']; ?>!</h2>
    </div>
    <div class="col-md-6 text-end">
        <span class="badge bg-primary me-2">Total Jobs: <?php echo $total_jobs; ?></span>
        <span class="badge bg-success">Applied: <?php echo $total_applied; ?></span>
    </div>
</div>
<h2 class="mb-4 text-center text-light">Available Jobs</h2>

<div class="row">

<?php
if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
?>

<div class="col-md-6 mb-4">
    <div class="card shadow-sm h-100">
        <div class="card-body">

            <!-- Title -->
            <h5 class="job-title"><?php echo $row['title']; ?></h5>
            <hr>

            <!-- Description Preview -->
            <div style="max-height:120px; overflow:auto;">
                <?php echo $row['description']; ?>
            </div>

            <hr>

            <!-- Job Details -->
            <p class="mb-1"><strong>💼 Experience:</strong> <?php echo $row['required_experience']; ?> years</p>
            <p class="mb-1"><strong>📍 Location:</strong> <?php echo $row['location']; ?></p>
            <p class="mb-1"><strong>💰 Salary:</strong> ₹<?php echo $row['salary']; ?></p>
            <p class="mb-1"><strong>🏷 Category:</strong> <?php echo $row['category']; ?></p>
            <p class="mb-1"><strong>📅 Deadline:</strong> <?php echo $row['deadline']; ?></p>

            <hr>

            <?php
            $check_apply = mysqli_query($conn,
                "SELECT status, hr_remark, offer_letter FROM applications 
                 WHERE job_id=".$row['id']." 
                 AND candidate_id=".$user_id);

            if(mysqli_num_rows($check_apply) > 0){

            $app_data = mysqli_fetch_assoc($check_apply);

            $status = $app_data['status'];
            $hr_remark = $app_data['hr_remark'];
            $offer_letter = $app_data['offer_letter'];


            // ✅ PROGRESS TRACKER
            $steps = ["Applied", "Shortlisted", "Interview Scheduled", "Selected"];
            $current_index = getStepIndex($status);

            echo "<div class='progress mb-2' style='height:8px;'>
                    <div class='progress-bar bg-success' 
                        style='width: ".(($current_index+1)*25)."%;'>
                    </div>
                </div>";

            echo "<div class='d-flex justify-content-between small text-center mb-2'>";

            foreach($steps as $index => $step){
                if($index <= $current_index){
                    echo "<div style='width:25%'>
                            <span class='text-success'>✔</span><br>
                            <span class='text-success'>$step</span>
                        </div>";
                } else {
                    echo "<div style='width:25%'>
                            <span class='text-muted'>○</span><br>
                            <span class='text-muted'>$step</span>
                        </div>";
                }
            }

            echo "</div>";

            // ❌ Rejected case
            if($status == "Rejected"){
                echo "<div class='alert alert-danger p-2'>
                        ❌ Application Rejected, Please wait for the HR remark for Rejection
                    </div>";
            }

            // ✅ SHOW REMARK
            if(!empty($hr_remark)){
                echo "<div class='alert alert-info p-2'>
                        <strong>HR Remark:</strong> $hr_remark
                    </div>";
            }
            
            if(!empty($offer_letter)){
            echo "<a href='../offers/".$offer_letter."' 
            target='_blank' 
            class='btn btn-success w-100 mt-2'>
            📄 Download Offer Letter
            </a>";
}
            echo "<button class='btn btn-secondary w-100' disabled>Already Applied</button>";

            } else {
                echo "<a href='apply_job.php?job_id=".$row['id']."' 
                         class='btn btn-success w-100'>Apply Now</a>";
            }
            ?>

        </div>
    </div>
</div>

<?php
    }
} else {
    echo "<div class='alert alert-info text-center'>
            No jobs available right now.
          </div>";
}
?>

</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>