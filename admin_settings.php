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
$message = "";

// 2. UPDATE SETTINGS LOGIC
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_settings'])) {
    $new_username = trim($_POST['new_username']);
    
    if (!empty($new_username)) {
        $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt->bind_param("si", $new_username, $_SESSION["user_id"]);
        
        if ($stmt->execute()) {
            $_SESSION["username"] = $new_username;
            $username = $new_username;
            $message = "Admin settings updated successfully!";
        } else {
            $message = "Failed to update settings. Username might already be taken.";
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
    <title>Admin - Settings</title>
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
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="admin_teachers.php">Teachers</a></li>
            <li><a href="admin_students.php">Students</a></li>
            <li><a href="admin_settings.php" class="active">Settings</a></li>
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
            <h1>Admin Settings</h1>
            <p>Manage your administrator account details.</p>
        </div>

        <?php if (!empty($message)): ?>
            <div style="background: #fef3c7; color: #92400e; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Form para sa Settings -->
        <div class="admin-user-list-card">
            <h3>Update Account Information</h3>
            <form method="POST" action="" style="display: flex; flex-direction: column; gap: 12px; margin-top: 15px;">
                <label style="font-weight: bold; color: #4c1d95;">Username:</label>
                <input type="text" name="new_username" value="<?php echo htmlspecialchars($username); ?>" required style="padding: 10px; border-radius: 8px; border: 1px solid #d1d5db;">

                <button type="submit" name="update_settings" style="background: #7e22ce; color: white; padding: 10px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 10px;">Save Changes</button>
            </form>
        </div>
    </div>

</body>
</html>