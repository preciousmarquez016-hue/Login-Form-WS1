<?php
session_start();
if (!isset($_SESSION["user_id"]) || strtolower($_SESSION["role"]) !== "student") {
    header("Location: signin.php");
    exit();
}
$username = $_SESSION["username"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student - Class Schedule</title>
    <link rel="stylesheet" href="student_style.css">
</head>
<body class="student-body">

    <!-- Sidebar Navigation -->
    <div class="student-sidebar">
        <div class="student-sidebar-logo">
            <img src="logo.png" alt="Logo">
            <h2>Student Dashboard</h2>
        </div>

        <ul class="student-nav-links">
            <li><a href="student_dashboard.php">Dashboard</a></li>
            <li><a href="student_grades.php">My Grades</a></li>
            <li><a href="student_schedule.php" class="active">Class Schedule</a></li>
            <li><a href="student_modules.php">Learning Modules</a></li>
            <li><a href="student_profile.php">Profile</a></li>
        </ul>

        <div class="student-sidebar-logout">
            <a href="logout.php" style="text-decoration: none;">
                <button type="button">Logout</button>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="student-main-content">
        <div class="student-header-card">
            <h1>Class Schedule</h1>
            <p>View your weekly class timetables and room assignments.</p>
        </div>

        <div class="student-content-card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-top: 20px;">
            <h3 style="color: #4c1d95; margin-bottom: 10px;">Enrolled Classes</h3>
            <p style="color: #4b5563; font-style: italic;">No schedule posted yet. Please check back later.</p>
        </div>
    </div>

</body>
</html>