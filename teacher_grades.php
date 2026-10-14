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

// 1. ADD GRADE LOGIC
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['save_grade'])) {
    $student_id = $_POST['student_id'];
    $subject = trim($_POST['subject']);
    $grade = $_POST['grade'];

    if (empty($student_id) || empty($subject) || $grade === "") {
        $message = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO grades (student_id, subject, grade) VALUES (?, ?, ?)");
        $stmt->bind_param("isd", $student_id, $subject, $grade);
        if ($stmt->execute()) {
            $message = "Grade saved successfully!";
        } else {
            $message = "Failed to save grade.";
        }
        $stmt->close();
    }
}

// 2. DELETE GRADE LOGIC
if (isset($_GET['delete_grade'])) {
    $grade_id = $_GET['delete_grade'];
    $stmt = $conn->prepare("DELETE FROM grades WHERE id = ?");
    $stmt->bind_param("i", $grade_id);
    $stmt->execute();
    $stmt->close();
    header("Location: teacher_grades.php");
    exit();
}

// Kunin ang listahan ng mga estudyante para sa dropdown
$students_query = $conn->query("SELECT id, first_name, last_name, username FROM users WHERE LOWER(role) = 'student' OR role IS NULL OR role = ''");

// Kunin ang lahat ng grades na nailagay na
$grades_query = $conn->query("
    SELECT grades.id, users.first_name, users.last_name, users.username, grades.subject, grades.grade 
    FROM grades 
    JOIN users ON grades.student_id = users.id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher - Grades</title>
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
            <li><a href="teacher_grades.php" class="active">Grades</a></li>
            <li><a href="teacher_classes.php">Classes</a></li>
            <li><a href="teacher_students.php">Students</a></li>
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
            <h1>Manage Student Grades</h1>
            <p>Input and monitor student academic records.</p>
        </div>

        <?php if (!empty($message)): ?>
            <div style="background: #fef3c7; color: #92400e; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Form para maglagay ng Grade -->
        <div class="teacher-content-card">
            <h3>Input Student Grade</h3>
            <form method="POST" action="" style="display: flex; flex-direction: column; gap: 12px; margin-top: 15px;">
                <label>Select Student:</label>
                <select name="student_id" required style="padding: 10px; border-radius: 8px; border: 1px solid #d1d5db;">
                    <option value="">-- Choose Student --</option>
                    <?php while ($stu = $students_query->fetch_assoc()): ?>
                        <option value="<?php echo $stu['id']; ?>">
                            <?php echo htmlspecialchars(($stu['first_name'] ?? '') . ' ' . ($stu['last_name'] ?? '') . ' (' . $stu['username'] . ')'); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label>Subject Name:</label>
                <input type="text" name="subject" placeholder="e.g. Information Technology" required style="padding: 10px; border-radius: 8px; border: 1px solid #d1d5db;">

                <label>Grade:</label>
                <input type="number" step="0.01" name="grade" placeholder="e.g. 1.25 or 95.00" required style="padding: 10px; border-radius: 8px; border: 1px solid #d1d5db;">

                <button type="submit" name="save_grade" style="background: #7e22ce; color: white; padding: 10px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">Save Grade</button>
            </form>
        </div>

        <!-- Listahan ng mga Grades -->
        <div class="teacher-content-card" style="margin-top: 25px;">
            <h3>Student Grades Record</h3>
            <?php if ($grades_query && $grades_query->num_rows > 0): ?>
                <table style="width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 14px;">
                    <thead>
                        <tr style="background: #faf5ff; color: #4c1d95;">
                            <th style="padding: 10px; border-bottom: 1px solid #f3e8ff; text-align: left;">Student Name</th>
                            <th style="padding: 10px; border-bottom: 1px solid #f3e8ff; text-align: left;">Subject</th>
                            <th style="padding: 10px; border-bottom: 1px solid #f3e8ff; text-align: left;">Grade</th>
                            <th style="padding: 10px; border-bottom: 1px solid #f3e8ff; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $grades_query->fetch_assoc()): ?>
                            <tr>
                                <td style="padding: 10px; border-bottom: 1px solid #f3e8ff;"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                <td style="padding: 10px; border-bottom: 1px solid #f3e8ff;"><?php echo htmlspecialchars($row['subject']); ?></td>
                                <td style="padding: 10px; border-bottom: 1px solid #f3e8ff;"><?php echo htmlspecialchars($row['grade']); ?></td>
                                <td style="padding: 10px; border-bottom: 1px solid #f3e8ff; text-align: center;">
                                    <a href="teacher_grades.php?delete_grade=<?php echo $row['id']; ?>" onclick="return confirm('Sigurado ka bang gusto mong burahin ang gradong ito?');" style="color: #dc3545; text-decoration: none; font-weight: bold;">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: #777; font-style: italic; margin-top: 10px;">No grades recorded yet.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>