<?php
include("../config.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../login.php");
    exit();
}

// Statistics
$total_jobs = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM jobs"))['total'];
$total_applications = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM applications"))['total'];
$total_candidates = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM users WHERE role='candidate'"))['total'];
$_SESSION['name'] = mysqli_fetch_assoc(mysqli_query($conn,"SELECT name FROM users WHERE id='".$_SESSION['user_id']."'"))['name'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
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

<div class="container mt-4">

        <h2>Welcome, <?php echo $_SESSION['name']; ?>!</h2><hr>

<div class="row">
    <div class="col-md-4">
        <div class="card text-bg-primary mb-3">
            <div class="card-body">
                <h5>Total Jobs</h5>
                <h3><?php echo $total_jobs; ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-bg-success mb-3">
            <div class="card-body">
                <h5>Total Applications</h5>
                <h3><?php echo $total_applications; ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-bg-info mb-3">
            <div class="card-body">
                <h5>Total Candidates</h5>
                <h3><?php echo $total_candidates; ?></h3>
            </div>
        </div>
    </div>
</div>
<a href="add_job.php" class="btn btn-primary">Add Job</a>
<a href="view_applications.php" class="btn btn-dark">View Applications</a>

<div class="d-flex justify-content-between mb-3">
    <h3>Posted Jobs</h3>
    
    <a href="deleted_jobs.php" class="btn btn-warning">
        View Deleted Jobs (Trash)
    </a>
</div>

<hr>

<h3>All Posted Jobs</h3>

<div class="row">
<?php
$result = mysqli_query($conn,"SELECT * FROM jobs WHERE is_deleted=0 ORDER BY created_at DESC");

while($row = mysqli_fetch_assoc($result)){
?>
    
<div class="col-md-6 mb-4">
    <div class="card shadow-sm h-100" style="border-radius:15px;">
        
        <div class="card-body">

            <!-- Title + Status -->
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title"><?php echo $row['title']; ?></h5>
                
                <?php if($row['status']=="Open"){ ?>
                    <span class="badge bg-success">Open</span>
                <?php } else { ?>
                    <span class="badge bg-danger">Closed</span>
                <?php } ?>
            </div>

            <hr>

            <!-- Description Preview -->
            <div style="max-height:100px; overflow:auto;">
                <?php echo $row['description']; ?>
            </div>

            <hr>

            <!-- Job Details -->
            <p><strong>📍 Location:</strong> <?php echo $row['location']; ?></p>
            <p><strong>💼 Experience:</strong> <?php echo $row['required_experience']; ?></p>
            <p><strong>💰 Salary:</strong> <?php echo $row['salary']; ?></p>
            <p><strong>📅 Deadline:</strong> <?php echo $row['deadline']; ?></p>
            <p><strong>🏷 Category:</strong> <?php echo $row['category']; ?></p>

        </div>

        <!-- Footer Buttons -->
        <div class="card-footer bg-white d-flex justify-content-between">

            <a href="edit_job.php?id=<?php echo $row['id']; ?>" 
               class="btn btn-warning btn-sm">✏ Edit</a>

            <a href="delete_job.php?id=<?php echo $row['id']; ?>" 
               class="btn btn-danger btn-sm"
               onclick="return confirm('Are you sure you want to delete this job?');">
               🗑 Delete
            </a>

        </div>
    </div>
</div>
<?php 
} ?>
<style>
.card:hover{
    transform: scale(1.02);
    transition: 0.3s;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>