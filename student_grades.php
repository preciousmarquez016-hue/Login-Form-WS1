<?php
session_start();
require_once "dp.php";

if (!isset($_SESSION["user_id"]) || strtolower($_SESSION["role"]) !== "student") {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Kunin ang mga grades ng estudyante mula sa database
$grades_query = $conn->prepare("SELECT subject, grade FROM grades WHERE student_id = ?");
$grades_query->bind_param("i", $user_id);
$grades_query->execute();
$grades_result = $grades_query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student - My Grades</title>
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
            <li><a href="student_grades.php" class="active">My Grades</a></li>
            <li><a href="student_schedule.php">Class Schedule</a></li>
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
            <h1>My Grades</h1>
            <p>View your academic performance and grades per subject.</p>
        </div>

        <div class="student-content-card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-top: 20px;">
            <h3 style="color: #4c1d95; margin-bottom: 15px;">Complete Grade Records</h3>
            <?php if ($grades_result && $grades_result->num_rows > 0): ?>
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr style="background: #faf5ff; color: #4c1d95;">
                            <th style="padding: 10px; border-bottom: 1px solid #f3e8ff; text-align: left;">Subject</th>
                            <th style="padding: 10px; border-bottom: 1px solid #f3e8ff; text-align: left;">Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $grades_result->fetch_assoc()): ?>
                            <tr>
                                <td style="padding: 10px; border-bottom: 1px solid #f3e8ff;"><?php echo htmlspecialchars($row['subject']); ?></td>
                                <td style="padding: 10px; border-bottom: 1px solid #f3e8ff; font-weight: bold; color: #7e22ce;"><?php echo htmlspecialchars($row['grade']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: #777; font-style: italic;">No grades available yet.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>