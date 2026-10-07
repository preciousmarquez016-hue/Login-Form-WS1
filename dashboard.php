<?php
session_start();
require_once "dp.php";

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

$students_query = $conn->query("SELECT first_name, last_name, username, email FROM users WHERE LOWER(role) = 'student' OR role IS NULL OR role = ''");
$teachers_query = $conn->query("SELECT first_name, last_name, username, email FROM users WHERE LOWER(role) = 'teacher'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body class="admin-body">

    <div class="admin-sidebar">
        <div class="admin-sidebar-logo">
            <img src="logo.png" alt="Logo">
            <h2>Admin Dashboard</h2>
        </div>

        <ul class="admin-nav-links">
            <li><a href="#" class="active">Dashboard</a></li>
            <li><a href="#teachers">Teachers</a></li>
            <li><a href="#students">Students</a></li>
            <li><a href="#">Settings</a></li>
        </ul>

        <div class="admin-sidebar-logout">
            <a href="logout.php" style="text-decoration: none;">
                <button type="button">Logout</button>
            </a>
        </div>
    </div>

    <div class="admin-main-content">

        <div class="admin-header-card">
            <h1>Welcome Back, <?php echo htmlspecialchars($username); ?>!</h1>
            <p>Manage your registered system accounts below.</p>
        </div>

        <div class="admin-user-list-card" id="teachers">
            <h3>Registered Teachers</h3>
            <?php if ($teachers_query && $teachers_query->num_rows > 0): ?>
                <table class="admin-user-table">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($teacher = $teachers_query->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(($teacher['first_name'] ?? '') . ' ' . ($teacher['last_name'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars($teacher['username']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="admin-no-data">No teachers registered yet.</p>
            <?php endif; ?>
        </div>

        <div class="admin-user-list-card" id="students">
            <h3>Registered Students</h3>
            <?php if ($students_query && $students_query->num_rows > 0): ?>
                <table class="admin-user-table">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($student = $students_query->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars($student['username']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="admin-no-data">No students registered yet.</p>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>