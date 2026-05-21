<?php
include("../config.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../login.php");
    exit();
}

$success = "";

if(isset($_POST['add_job'])){

    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $salary = $_POST['salary'];
    $experience = $_POST['experience'];
    $category = $_POST['category'];
    $deadline = $_POST['deadline'];
    $status = $_POST['status'];

    mysqli_query($conn,"INSERT INTO jobs 
    (title, description, location, salary, required_experience, category, deadline, status) 
    VALUES ('$title','$description','$location','$salary','$experience','$category','$deadline','$status')");

    $success = "Job added successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Job</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
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
            <li>
                <a class="dropdown-item" href="edit_profile.php">✏ Edit Profile</a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger" href="../logout.php">🚪 Logout</a>
            </li>
        </ul>
    </div>

</nav>

<div class="container mt-5">
    <div class="card shadow-lg p-4" style="max-width:700px; margin:auto; border-radius:15px;">

        <h3 class="mb-4 text-center">Create New Job</h3>

        <?php if($success != ""){ ?>
            <div class="alert alert-success text-center">
                <?php echo $success; ?>
            </div>
        <?php } ?>

        <form method="POST">

        <div class="mb-3">
            <label class="form-label">Job Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Job Category</label>
            <select name="category" class="form-select" required>
                <option value="">Select Category</option>
                <option>IT</option>
                <option>Marketing</option>
                <option>Finance</option>
                <option>HR</option>
                <option>Operations</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Experience (Years)</label>
            <input type="text" name="experience" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Salary</label>
            <input type="text" name="salary" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Application Deadline</label>
            <input type="date" name="deadline" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Job Status</label><br>
            <input type="radio" name="status" value="Open" checked> Open
            <input type="radio" name="status" value="Closed"> Closed
        </div>

        <div class="mb-3">
            <label class="form-label">Job Description</label>
            <textarea name="description" id="editor" class="form-control"></textarea>
        </div>

        <div class="d-flex justify-content-between">
            <a href="dashboard.php" class="btn btn-secondary">⬅ Back</a>
            <button type="submit" name="add_job" class="btn btn-primary">Post Job</button>
        </div>

        </form>
    </div>
</div>

<script>
    CKEDITOR.replace('editor');
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>