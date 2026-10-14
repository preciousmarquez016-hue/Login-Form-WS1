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

// 2. CREATE TEACHER LOGIC (Pag nag-submit ng form para magdagdag ng guro)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_teacher'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $new_username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = 'teacher'; // Itakda bilang guro

    if (empty($new_username) || empty($email) || empty($password)) {
        $message = "Please fill in all required fields.";
    } else {
        // I-hash ang password para sa seguridad
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, email, password, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $first_name, $last_name, $new_username, $email, $hashed_password, $role);
        
        if ($stmt->execute()) {
            $message = "Teacher account created successfully!";
        } else {
            $message = "Error: Username or Email might already exist.";
        }
        $stmt->close();
    }
}

// 3. Kunin ang listahan ng mga guro mula sa database
$teachers_query = $conn->query("SELECT id, first_name, last_name, username, email FROM users WHERE LOWER(role) = 'teacher'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Teachers</title>
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
            <li><a href="admin_teachers.php" class="active">Teachers</a></li>
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
            <h1>Manage Teachers</h1>
            <p>Add new teacher accounts and view registered instructors.</p>
        </div>

        <?php if (!empty($message)): ?>
            <div style="background: #fef3c7; color: #92400e; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Form para magdagdag ng Teacher -->
        <div class="admin-user-list-card">
            <h3>Add New Teacher</h3>
            <form method="POST" action="" style="display: flex; flex-direction: column; gap: 12px; margin-top: 15px;">
                <div style="display: flex; gap: 10px;">
                    <input type="text" name="first_name" placeholder="First Name" style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #d1d5db;">
                    <input type="text" name="last_name" placeholder="Last Name" style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #d1d5db;">
                </div>
                <input type="text" name="username" placeholder="Username" required style="padding: 10px; border-radius: 8px; border: 1px solid #d1d5db;">
                <input type="email" name="email" placeholder="Email Address" required style="padding: 10px; border-radius: 8px; border: 1px solid #d1d5db;">
                <input type="password" name="password" placeholder="Password" required style="padding: 10px; border-radius: 8px; border: 1px solid #d1d5db;">
                
                <button type="submit" name="add_teacher" style="background: #7e22ce; color: white; padding: 10px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">Register Teacher</button>
            </form>
        </div>

        <!-- Listahan ng mga Teachers -->
        <div class="admin-user-list-card" style="margin-top: 25px;">
            <h3>Registered Teachers List</h3>
            <?php if ($teachers_query && $teachers_query->num_rows > 0): ?>
                <table class="admin-user-table" style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                    <thead>
                        <tr style="background: #faf5ff; color: #4c1d95;">
                            <th style="padding: 10px; text-align: left;">Full Name</th>
                            <th style="padding: 10px; text-align: left;">Username</th>
                            <th style="padding: 10px; text-align: left;">Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($teacher = $teachers_query->fetch_assoc()): ?>
                            <tr>
                                <td style="padding: 10px; border-bottom: 1px solid #f3e8ff;"><?php echo htmlspecialchars(($teacher['first_name'] ?? '') . ' ' . ($teacher['last_name'] ?? '')); ?></td>
                                <td style="padding: 10px; border-bottom: 1px solid #f3e8ff;"><?php echo htmlspecialchars($teacher['username']); ?></td>
                                <td style="padding: 10px; border-bottom: 1px solid #f3e8ff;"><?php echo htmlspecialchars($teacher['email']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="admin-no-data" style="margin-top: 15px; color: #777; font-style: italic;">No teachers registered yet.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>