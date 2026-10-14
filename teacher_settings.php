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
$message = "";

// Password change logic (Optional kung gusto mong lagyan ng feature)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_settings'])) {
    $new_username = trim($_POST['username']);
    if (!empty($new_username)) {
        $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt->bind_param("si", $new_username, $_SESSION["user_id"]);
        if ($stmt->execute()) {
            $_SESSION["username"] = $new_username;
            $username = $new_username;
            $message = "Settings updated successfully!";
        } else {
            $message = "Failed to update settings.";
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
    <title>Teacher - Settings</title>
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
            <li><a href="teacher_students.php">Students</a></li>
            <li><a href="teacher_settings.php" class="active">Settings</a></li>
        </ul>

        <div class="teacher-sidebar-logout">
            <a href="logout.php" style="text-decoration: none;">
                <button type="button">Logout</button>
            </a>
        </div>
    </div>

    <div class="teacher-main-content">
        <div class="teacher-header-card">
            <h1>Account Settings</h1>
            <p>Manage your account preferences and credentials.</p>
        </div>

        <?php if (!empty($message)): ?>
            <div style="background: #fef3c7; color: #92400e; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="teacher-content-card">
            <h3>Profile Settings</h3>
            <form method="POST" action="" style="display: flex; flex-direction: column; gap: 12px; margin-top: 15px;">
                <label>Username:</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required style="padding: 10px; border-radius: 8px; border: 1px solid #d1d5db;">

                <button type="submit" name="update_settings" style="background: #7e22ce; color: white; padding: 10px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">Save Changes</button>
            </form>
        </div>
    </div>

</body>
</html>