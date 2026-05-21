<?php
include("../config.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != "super_admin"){
    die("Access Denied!");
}
$_SESSION['name'] = mysqli_fetch_assoc(mysqli_query($conn,"SELECT name FROM users WHERE id='".$_SESSION['user_id']."'"))['name'];
$result = mysqli_query($conn,"SELECT * FROM users WHERE id='".$_SESSION['user_id']."'");
$user = mysqli_fetch_assoc($result);
$search = $_GET['search'] ?? '';
$search_query = "";
$limit = 5;
$page = $_GET['page'] ?? 1;
$start = ($page - 1) * $limit;

if($search){
    $search_query = " AND (name LIKE '%$search%' OR email LIKE '%$search%')";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Super Admin Dashboard</title>
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
            <li>
                <a class="dropdown-item" href="../admin_register.php">✏ Create Admin</a>
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
</div><hr>

<?php
$total_admins = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM users WHERE role='admin' AND is_deleted=0"
))['total'];

$total_candidates = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM users WHERE role='candidate' AND is_deleted=0"
))['total'];
?>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card text-white bg-primary shadow">
            <div class="card-body text-center">
                <h5>Total Admins</h5>
                <h2><?php echo $total_admins; ?></h2>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card text-white bg-success shadow">
            <div class="card-body text-center">
                <h5>Total Candidates</h5>
                <h2><?php echo $total_candidates; ?></h2>
            </div>
        </div>
    </div>
</div>

<h2 class="mb-4 text-center">Super Admin Dashboard</h2>

<form method="GET" class="mb-3">
    <input type="text" name="search" class="form-control"
           placeholder="Search by name or email"
           value="<?php echo $_GET['search'] ?? ''; ?>">
</form>

<!-- Admin List -->
<h4>All Admins</h4>
<table class="table table-bordered">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Action</th>
</tr>

<?php
$admins = mysqli_query($conn,"SELECT * FROM users WHERE role='admin' AND is_deleted=0 $search_query LIMIT $start, $limit");
while($row = mysqli_fetch_assoc($admins)){
?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['name']; ?></td>
    <td><?php echo $row['email']; ?></td>
    <td>
        <a href="delete_user.php?id=<?php echo $row['id']; ?>"
           class="btn btn-danger btn-sm"
           onclick="return confirm('Are you sure you want to delete this admin?')">
           Delete
        </a>
    </td>
</tr>
<?php } ?>
</table>

<!-- Candidate List -->
<h4 class="mt-5">All Candidates</h4>
<table class="table table-bordered">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Action</th>
</tr>

<?php
$candidates = mysqli_query($conn,"SELECT * FROM users WHERE role='candidate' AND is_deleted=0 $search_query LIMIT $start, $limit");
while($row = mysqli_fetch_assoc($candidates)){
?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['name']; ?></td>
    <td><?php echo $row['email']; ?></td>
    <td>
        <a href="delete_user.php?id=<?php echo $row['id']; ?>"
           class="btn btn-danger btn-sm"
           onclick="return confirm('Are you sure you want to delete this candidate?')">
           Delete
        </a>
    </td>
</tr>
<?php } ?>
</table>

<?php
$total_result = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM users WHERE role='admin' AND is_deleted=0 $search_query"
));
$total_pages = ceil($total_result['total'] / $limit);

for($i=1; $i<=$total_pages; $i++){
    echo "<a href='?page=$i&search=$search' class='btn btn-sm btn-outline-dark m-1'>$i</a>";
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</div>
</body>
</html>