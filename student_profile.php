<?php
session_start();
require_once "dp.php";

if (!isset($_SESSION["user_id"]) || strtolower($_SESSION["role"]) !== "student") {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"];
$message = "";

// UPDATE PROFILE LOGIC
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_profile'])) {
    $new_username = trim($_POST['new_username']);
    
    if (!empty($new_username)) {
        $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt->bind_param("si", $new_username, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION["username"] = $new_username;
            $username = $new_username;
            $message = "Profile updated successfully!";
        } else {
            $message = "Username might already be taken.";
        }
        $stmt->close();
    } else {
        $message = "Username cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student - Profile</title>
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
            <li><a href="student_schedule.php">Class Schedule</a></li>
            <li><a href="student_modules.php">Learning Modules</a></li>
            <li><a href="student_profile.php" class="active">Profile</a></li>
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
            <h1>Student Profile</h1>
            <p>Manage your account settings and personal info.</p>
        </div>

        <?php if (!empty(trim($message))): ?>
            <div style="background: #fef3c7; color: #92400e; padding: 12px; border-radius: 8px; margin-top: 20px;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="student-content-card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-top: 20px;">
            <h3 style="color: #4c1d95; margin-bottom: 15px;">Update Username</h3>
            <form method="POST" action="" style="display: flex; flex-direction: column; gap: 12px;">
                <label style="font-weight: bold; color: #4c1d95;">Username:</label>
                <input type="text" name="new_username" value="<?php echo htmlspecialchars($username); ?>" required style="padding: 10px; border-radius: 8px; border: 1px solid #d1d5db;">

                <button type="submit" name="update_profile" style="background: #7e22ce; color: white; padding: 10px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 10px;">Save Changes</button>
            </form>
        </div>
    </div>

</body>
</html>