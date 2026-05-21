<?php
include("../config.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'];

$result = mysqli_query($conn,"SELECT * FROM jobs WHERE id='$id'");
$row = mysqli_fetch_assoc($result);

if(isset($_POST['update_job'])){

    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $salary = $_POST['salary'];
    $experience = $_POST['experience'];
    $category = $_POST['category'];
    $deadline = $_POST['deadline'];
    $status = $_POST['status'];

    mysqli_query($conn,"UPDATE jobs SET 
        title='$title',
        description='$description',
        location='$location',
        salary='$salary',
        required_experience='$experience',
        category='$category',
        deadline='$deadline',
        status='$status'
        WHERE id='$id'
    ");

    echo "<script>alert('Job Updated Successfully!'); window.location='dashboard.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Job</title>
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
<div class="card shadow-lg p-4" style="max-width:700px; margin:auto; border-radius:15px;">

<h3 class="mb-4 text-center">Edit Job</h3>

<form method="POST">

<div class="mb-3">
<label class="form-label">Job Title</label>
<input type="text" name="title" class="form-control"
value="<?php echo $row['title']; ?>" required>
</div>

<div class="mb-3">
<label class="form-label">Job Category</label>
<select name="category" class="form-select" required>
<option <?php if($row['category']=="IT") echo "selected"; ?>>IT</option>
<option <?php if($row['category']=="Marketing") echo "selected"; ?>>Marketing</option>
<option <?php if($row['category']=="Finance") echo "selected"; ?>>Finance</option>
<option <?php if($row['category']=="HR") echo "selected"; ?>>HR</option>
<option <?php if($row['category']=="Operations") echo "selected"; ?>>Operations</option>
</select>
</div>

<div class="mb-3">
<label class="form-label">Experience</label>
<input type="text" name="experience" class="form-control"
value="<?php echo $row['required_experience']; ?>" required>
</div>

<div class="mb-3">
<label class="form-label">Location</label>
<input type="text" name="location" class="form-control"
value="<?php echo $row['location']; ?>" required>
</div>

<div class="mb-3">
<label class="form-label">Salary</label>
<input type="text" name="salary" class="form-control"
value="<?php echo $row['salary']; ?>">
</div>

<div class="mb-3">
<label class="form-label">Deadline</label>
<input type="date" name="deadline" class="form-control"
value="<?php echo $row['deadline']; ?>" required>
</div>

<div class="mb-3">
<label class="form-label">Status</label><br>
<input type="radio" name="status" value="Open"
<?php if($row['status']=="Open") echo "checked"; ?>> Open
<input type="radio" name="status" value="Closed"
<?php if($row['status']=="Closed") echo "checked"; ?>> Closed
</div>

<!-- 🔥 DESCRIPTION FIELD SAME AS ADD JOB -->

<div class="mb-3">
<label class="form-label">Job Description</label>
<textarea name="description" id="editor" class="form-control">
<?php echo $row['description']; ?>
</textarea>
</div>

<div class="d-flex justify-content-between">
<a href="dashboard.php" class="btn btn-secondary">⬅ Back</a>
<button type="submit" name="update_job" class="btn btn-success">Update Job</button>
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