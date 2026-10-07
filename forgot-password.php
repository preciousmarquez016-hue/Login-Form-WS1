<?php
require_once "dp.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $newPassword = $_POST["new_password"];

    if (empty($username) || empty($email) || empty($newPassword)) {

        $message = "Please fill in all fields.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id FROM users WHERE username = ? AND email = ?"
        );

        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $hashedPassword = password_hash(
                $newPassword,
                PASSWORD_DEFAULT
            );

            $update = $conn->prepare(
                "UPDATE users SET password = ? WHERE username = ? AND email = ?"
            );

            $update->bind_param(
                "sss",
                $hashedPassword,
                $username,
                $email
            );

            if ($update->execute()) {
                $message = "Password changed successfully! You can now sign in.";
            } else {
                $message = "Failed to change password.";
            }

            $update->close();

        } else {

            $message = "Username and email do not match.";

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

    <title>Forgot Password</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <div class="logo">
        <img src="logo.png" alt="Logo">
    </div>

    <h1>Forgot Password</h1>

    <p>Reset your password</p>

    <?php if (!empty($message)): ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <label>Username</label>

        <input
            type="text"
            name="username"
            placeholder="Enter username"
            required
        >

        <label>Email</label>

        <input
            type="email"
            name="email"
            placeholder="Enter email"
            required
        >

        <label>New Password</label>

        <input
            type="password"
            name="new_password"
            placeholder="Enter new password"
            required
        >

        <button type="submit">
            Reset Password
        </button>

    </form>

    <p>
        Remember your password?
        <a href="signin.php">Sign In</a>
    </p>

</div>

</body>

</html>