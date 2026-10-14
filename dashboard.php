<?php
session_start();
require_once "dp.php";

// 1. Security Check: Siguraduhing admin ang naka-login
if (!isset($_SESSION["user_id"])) {
    header("Location: signin.php");
    exit();
}

if (isset($_SESSION["role"]) && strtolower($_SESSION["role"]) !== "admin") {
    if ($_SESSION["role"] === "teacher") {
        header("Location: teacher_dashboard.php");
    } else {
        header("Location: student_dashboard.php");
    }
    exit();
}

$username = $_SESSION["username"];

$teachers_count_query = $conn->query("SELECT COUNT(*) as total FROM users WHERE LOWER(role) = 'teacher'");
$teachers_count = $teachers_count_query->fetch_assoc()['total'] ?? 0;

$students_count_query = $conn->query("SELECT COUNT(*) as total FROM users WHERE LOWER(role) = 'student' OR role IS NULL OR role = ''");
$students_count = $students_count_query->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Overview</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body class="admin-body">

    <!-- Sidebar Navigation -->
    <div class="admin-sidebar">
        <div class="admin-sidebar-logo">
            <img src="logo.png" alt="Logo">
            <h2>Admin Dashboard</h2>
        </div>

        <ul class="admin-nav-links">
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <li><a href="admin_teachers.php">Teachers</a></li>
            <li><a href="admin_students.php">Students</a></li>
            <li><a href="admin_settings.php">Settings</a></li>
        </ul>

        <div class="admin-sidebar-logout">
            <a href="logout.php" style="text-decoration: none;">
                <button type="button">Logout</button>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="admin-main-content">
        <div class="admin-header-card">
            <h1>Welcome Back, <?php echo htmlspecialchars($username); ?>!</h1>
            <p>System Overview & Quick Statistics Control Panel</p>
        </div>

        <!-- Quick Stats Cards -->
        <div style="display: flex; gap: 20px; margin-top: 25px;">
            <!-- Teachers Stats Card -->
            <div class="admin-user-list-card" style="flex: 1; text-align: center; padding: 25px;">
                <h3 style="color: #4c1d95; margin-bottom: 10px;">Total Registered Teachers</h3>
                <p style="font-size: 38px; font-weight: bold; color: #7e22ce; margin: 10px 0;"><?php echo $teachers_count; ?></p>
                <a href="admin_teachers.php" style="display: inline-block; margin-top: 15px; color: #7e22ce; text-decoration: none; font-weight: bold;">Manage Teachers →</a>
            </div>

            <!-- Students Stats Card -->
            <div class="admin-user-list-card" style="flex: 1; text-align: center; padding: 25px;">
                <h3 style="color: #4c1d95; margin-bottom: 10px;">Total Registered Students</h3>
                <p style="font-size: 38px; font-weight: bold; color: #7e22ce; margin: 10px 0;"><?php echo $students_count; ?></p>
                <a href="admin_students.php" style="display: inline-block; margin-top: 15px; color: #7e22ce; text-decoration: none; font-weight: bold;">Manage Students →</a>
            </div>
        </div>

        <!-- Quick Information Card -->
        <div class="admin-user-list-card" style="margin-top: 25px;">
            <h3 style="color: #4c1d95; margin-bottom: 10px;">Quick Guide</h3>
            <p style="color: #4b5563; line-height: 1.6;">
                Use the sidebar on the left to navigate to specific management sections:
            </p>
            <ul style="margin-top: 10px; margin-left: 20px; color: #4b5563; line-height: 1.6;">
                <li><strong>Teachers:</strong> To add and view the list of teachers.</li>
                <li><strong>Students:</strong> To add and view the list of students.</li>
                <li><strong>Settings:</strong> To update your admin account credentials.</li>
            </ul>
        </div>
    </div>

</body>
</html>
