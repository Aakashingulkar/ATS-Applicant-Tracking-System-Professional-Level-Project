<?php
// index.php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ATS - Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f8f9fa;
        }
        .hero {
            background: linear-gradient(to right, #b5d0f8, #6610f2);
            color: white;
            padding: 80px 20px;
            text-align: center;
        }
        .job-card {
            transition: 0.3s;
        }
        .job-card:hover {
            transform: scale(1.03);
        }
        footer {
            background: #212529;
            color: white;
            padding: 15px;
            text-align: center;
        }
        h2 {
            font-weight: bold;
            margin-bottom: 15px;
        }

        p {
            color: #555;
            line-height: 1.6;
        }
    </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top px-4" style="background: rgba(0,0,0,0.5); backdrop-filter: blur(6px);">

    <!-- Logo + Brand -->
    <a class="navbar-brand d-flex align-items-center" href="index.php">
        <img src="images/logo.png" alt="Logo" style="width:40px; height:40px; margin-right:10px;">
        Code Craft
    </a>
    <div class="ms-auto d-flex gap-3">
        <a href="index.php" class="nav-link text-white"><strong>Home</strong></a>
        <a href="login.php" class="nav-link text-white"><strong>Career</strong></a>
    </div>
</nav>

<!-- Hero Section -->
<div class="hero">
    <div class="Logo">
        <img src="images/logo.png" alt="logo" style="width=200px" height="200px">
    </div>

    <h1>Welcome to Code Craft</h1>
    <p>Your gateway to find jobs and manage applications easily</p>
    <a href="login.php" class="btn btn-light btn-lg mt-3">Browse Jobs</a>
</div>

<!-- Features -->
<div class="container mt-5">
    <h2 class="text-center mb-4">Features</h2>
    <div class="row text-center">

        <div class="col-md-4">
            <h4>Apply Jobs</h4>
            <p>Quickly apply to multiple job openings.</p>
        </div>

        <div class="col-md-4">
            <h4>Track Applications</h4>
            <p>Monitor your job application status.</p>
        </div>

        <div class="col-md-4">
            <h4>Interview Scheduling</h4>
            <p>Get notified and schedule interviews easily.</p>
        </div>

    </div>
</div>
<!-- About Us Section -->
    <div class="container mt-5">
        <div class="row align-items-center">

            <!-- Image -->
            <div class="col-md-6">
                <img src="images/about.png" class="img-fluid rounded shadow" alt="About ATS">
            </div>

            <!-- Content -->
            <div class="col-md-6">
                <h2>About Us</h2>
                <p>
                    Our Applicant Tracking System (ATS) is designed to simplify the hiring process 
                    for both recruiters and job seekers. We provide a smart platform to manage job 
                    applications, track candidate progress, and schedule interviews efficiently.
                </p>

                <p>
                    Whether you are a company looking for the right talent or a candidate searching 
                    for your dream job, our ATS makes the process fast, transparent, and hassle-free.
                </p>
            </div>

        </div>
    </div>

<!-- Footer -->
<footer class="mt-5">
    <p>© <?php echo date("Y"); ?> CodeCraft | Developed by Mayuri Gupta</p>
</footer>

</body>
</html>