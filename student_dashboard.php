<?php
session_start();
require_once "dp.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: signin.php");
    exit();
}

if (isset($_SESSION["role"]) && strtolower($_SESSION["role"]) !== "student") {
    if ($_SESSION["role"] === "admin") {
        header("Location: dashboard.php");
    } else {
        header("Location: teacher_dashboard.php");
    }
    exit();
}

$username = $_SESSION["username"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="student_style.css">
</head>
<body class="student-body">

    <div class="student-sidebar">
        <div class="student-sidebar-logo">
            <img src="logo.png" alt="Logo">
            <h2>Student Dashboard</h2>
        </div>

        <ul class="student-nav-links">
            <li><a href="#" class="active">Dashboard</a></li>
            <li><a href="#">My Grades</a></li>
            <li><a href="#">Class Schedule</a></li>
            <li><a href="#">Learning Modules</a></li>
            <li><a href="#">Profile</a></li>
        </ul>

        <div class="student-sidebar-logout">
            <a href="logout.php" style="text-decoration: none;">
                <button type="button">Logout</button>
            </a>
        </div>
    </div>

    <div class="student-main-content">
        <div class="student-header-card">
            <h1>Welcome Back, <?php echo htmlspecialchars($username); ?></h1>
            <p>Student Overview Panel</p>
        </div>

        <div class="student-content-card">
            <h3>Academic Status</h3>
            <p>Access your enrolled subjects, view schedules, and check grade evaluation.</p>
        </div>
    </div>

</body>
</html>