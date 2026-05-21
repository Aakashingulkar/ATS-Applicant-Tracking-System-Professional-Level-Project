<?php
include("../config.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../login.php");
    exit();
}

// SEARCH + PAGINATION
$search = $_GET['search'] ?? '';
$search = mysqli_real_escape_string($conn, $search);
$search_query = "";

$limit = 5;
$page = $_GET['page'] ?? 1;
$start = ($page - 1) * $limit;

// SEARCH CONDITION
if(!empty($search)){
    $search_query = "WHERE u.name LIKE '%$search%' 
                     OR j.title LIKE '%$search%' 
                     OR a.phone LIKE '%$search%'";
}

// SAVE HR_REMARK
if(isset($_POST['save_hr_remark'])){
    $app_id = $_POST['app_id'];
    $hr_remark = mysqli_real_escape_string($conn, $_POST['hr_remark']);

    mysqli_query($conn,"UPDATE applications SET hr_remark='$hr_remark' WHERE id='$app_id'");

    header("Location: view_applications.php?page=$page&search=$search");
    exit();
}

// Update Status
if(isset($_GET['update']) && isset($_GET['id'])){
    $id = $_GET['id'];
    $status = $_GET['update'];
    mysqli_query($conn,"UPDATE applications SET status='$status'  WHERE id='$id'");
    header("Location: view_applications.php?page=$page&search=$search");
}

$query = "SELECT a.*, u.name, j.title 
          FROM applications a
          JOIN users u ON a.candidate_id = u.id
          JOIN jobs j ON a.job_id = j.id
          $search_query
          LIMIT $start, $limit";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Applications</title>
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

<div class="container mt-4">
<h2>Applications</h2><hr>

<form method="GET" class="mb-3 d-flex">
    <input type="text" name="search" class="form-control me-2" 
           placeholder="Search by name, job, phone..." 
           value="<?php echo $search; ?>">
    <button type="submit" class="btn btn-primary">Search</button>
</form>

<table class="table table-bordered table-striped">
<tr>
    <th>Candidate</th>
    <th>Job</th>
    <th>Phone</th>
    <th>Experience</th>
    <th>Preferred Location</th>
    <th>Resume</th>
    <th>Status</th>
    <th>AI Score</th>
    <th>AI Remark</th>
    <th>Actions</th>
    <th>HR Remark</th>
    <th>Save</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
<form method="POST">

    <td><?php echo $row['name']; ?></td>
    <td><?php echo $row['title']; ?></td>
    <td><?php echo $row['phone']; ?></td>
    <td><?php echo $row['experience']; ?> Years</td>
    <td><?php echo $row['preferred_location']; ?></td>
    <td><a href="../uploads/<?php echo $row['resume']; ?>" target="_blank">View</a></td>

    <!-- STATUS -->
    <td>
        <span class="badge bg-<?php 
            echo ($row['status']=="Shortlisted") ? "success" : 
                (($row['status']=="Rejected") ? "danger" : "secondary");
        ?>">
            <?php echo $row['status']; ?>
        </span>
    </td>

    <!-- AI SCORE -->
    <td>
        <?php
        $score = $row['screening_score'];
        $color = ($score >= 75) ? "success" : (($score >= 50) ? "warning" : "danger");
        ?>
        <span class="badge bg-<?php echo $color; ?>">
            <?php echo $score ? $score."%" : "N/A"; ?>
        </span>
    </td>

    <!-- AI REMARK -->
    <td style="max-width:250px;">
        <?php 
        if(!empty($row['remark'])){
            echo "<div class='alert alert-info p-2 mb-0' style='font-size:12px;'>{$row['remark']}</div>";
        }else{
            echo "<span class='text-muted'>No AI Feedback</span>";
        }
        ?>
    </td>

    <!-- ACTIONS -->
    <td>
        <a href="?update=Shortlisted&id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">Shortlist</a><hr>
        <a href="schedule_interview.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Schedule Interview</a><hr>
        <a href="?update=Rejected&id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Reject</a><hr>
        <a href="generate_offer.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Select & Offer</a>
    </td>

<!-- HR_REMARK + SAVE -->
    <form method="POST">
        <td>
            <input type="hidden" name="app_id" value="<?php echo $row['id']; ?>">

            <textarea name="hr_remark" class="form-control" rows="5" cols="70" 
            placeholder="Enter remark..."><?php echo $row['hr_remark']; ?></textarea>
        </td>

        <td>
            <button type="submit" name="save_hr_remark" class="btn btn-primary btn-sm">
                Save
            </button>
        </td>
    </form>
</tr>
<?php } ?>

</table>

<?php
// TOTAL RECORDS
$total_query = mysqli_query($conn,"SELECT COUNT(*) as total 
                                  FROM applications a
                                  JOIN users u ON a.candidate_id = u.id
                                  JOIN jobs j ON a.job_id = j.id
                                  $search_query");

$total_row = mysqli_fetch_assoc($total_query);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);
?>

<nav>
<ul class="pagination">
<?php for($i=1; $i<=$total_pages; $i++){ ?>
    <li class="page-item <?php if($i==$page) echo 'active'; ?>">
        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>">
            <?php echo $i; ?>
        </a>
    </li>
<?php } ?>
</ul>
</nav>

<a href="dashboard.php" class="btn btn-secondary">Back</a>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>