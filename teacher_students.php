<?php
session_start();
require_once "dp.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: signin.php");
    exit();
}

if (isset($_SESSION["role"]) && strtolower($_SESSION["role"]) !== "teacher") {
    if ($_SESSION["role"] === "admin") {
        header("Location: dashboard.php");
    } else {
        header("Location: student_dashboard.php");
    }
    exit();
}

$username = $_SESSION["username"];

// Kunin ang listahan ng lahat ng mga estudyante mula sa database
$students_query = $conn->query("SELECT id, first_name, last_name, username FROM users WHERE LOWER(role) = 'student' OR role IS NULL OR role = ''");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher - Students</title>
    <link rel="stylesheet" href="teacher_style.css">
</head>
<body class="teacher-body">

    <div class="teacher-sidebar">
        <div class="teacher-sidebar-logo">
            <img src="logo.png" alt="Logo">
            <h2>Teacher Dashboard</h2>
        </div>

        <ul class="teacher-nav-links">
            <li><a href="teacher_dashboard.php">Dashboard</a></li>
            <li><a href="teacher_grades.php">Grades</a></li>
            <li><a href="teacher_classes.php">Classes</a></li>
            <li><a href="teacher_students.php" class="active">Students</a></li>
            <li><a href="teacher_settings.php">Settings</a></li>
        </ul>

        <div class="teacher-sidebar-logout">
            <a href="logout.php" style="text-decoration: none;">
                <button type="button">Logout</button>
            </a>
        </div>
    </div>

    <div class="teacher-main-content">
        <div class="teacher-header-card">
            <h1>Enrolled Students</h1>
            <p>List of all students registered in the system.</p>
        </div>

        <div class="teacher-content-card">
            <h3>Student Directory</h3>
            <table style="width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 14px;">
                <thead>
                    <tr style="background: #faf5ff; color: #4c1d95;">
                        <th style="padding: 10px; border-bottom: 1px solid #f3e8ff; text-align: left;">Student Name</th>
                        <th style="padding: 10px; border-bottom: 1px solid #f3e8ff; text-align: left;">Username / Account</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($students_query && $students_query->num_rows > 0): ?>
                        <?php while ($row = $students_query->fetch_assoc()): ?>
                            <tr>
                                <td style="padding: 10px; border-bottom: 1px solid #f3e8ff;"><?php echo htmlspecialchars(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')); ?></td>
                                <td style="padding: 10px; border-bottom: 1px solid #f3e8ff;"><?php echo htmlspecialchars($row['username']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" style="padding: 10px; color: #777; font-style: italic;">No students found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>