<?php
session_start();
require_once "dp.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if (empty($username) || empty($password)) {
        $message = "Please enter your username and password.";
    } else {

        $stmt = $conn->prepare(
            "SELECT id, username, password, role FROM users WHERE username = ?"
        );

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                
                $user_role = strtolower(trim($user["role"] ?? "student"));
                $_SESSION["role"] = $user_role;

                if ($user_role === "admin") {
                    header("Location: dashboard.php");
                } elseif ($user_role === "teacher") {
                    header("Location: teacher_dashboard.php");
                } else {
                    header("Location: student_dashboard.php");
                }
                exit();

            } else {
                $message = "Incorrect username or password.";
            }

        } else {
            $message = "Incorrect username or password.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <div class="logo">
        <img src="logo.png" alt="Logo">
    </div>

    <h1>Welcome Back</h1>
    <p>Sign in to your account</p>

    <?php if (!empty($message)): ?>
        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">

        <label for="username">Username</label>
        <input
            type="text"
            id="username"
            name="username"
            placeholder="Enter username"
            required
        >

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter password"
            required
        >

        <button type="submit">Sign In</button>

    </form>

    <p style="margin-top: 20px;">
        <a href="forgot-password.php">Forgot Password?</a>
    </p>

    <p>
        Don't have an account?
        <a href="signup.php">Create Account</a>
    </p>
    <p>
        <h4>ADMIN username: admin <br>
             password:123</h4>
        <h4>TEACHER username: teacher <br>
             password:123</h4>
        <h4>STUDENT username: student <br>
             password:123</h4>
    </p>
   

    

</div>

</body>
</html>