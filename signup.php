<?php
session_start();
require_once "dp.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $firstName = trim($_POST["first_name"]);
    $lastName = trim($_POST["last_name"]);
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $role = "student"; // Direct to 'student' only

    if (empty($firstName) || empty($lastName) || empty($username) || empty($email) || empty($password)) {
        $message = "Please fill in all fields.";
    } else {

        // Check kung umiiral na ang username o email
        $check = $conn->prepare(
            "SELECT id FROM users WHERE username = ? OR email = ?"
        );
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $message = "Username or email already exists.";
        } else {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Isinama ang first_name, last_name, at automatic student role
            $stmt = $conn->prepare(
                "INSERT INTO users (first_name, last_name, username, email, password, role) VALUES (?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssssss",
                $firstName,
                $lastName,
                $username,
                $email,
                $hashedPassword,
                $role
            );

            if ($stmt->execute()) {
                $message = "Registration successful! You can now sign in.";
            } else {
                $message = "Registration failed. Please try again.";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container" style="margin: 20px auto;">

    <div class="logo">
        <img src="logo.png" alt="Logo">
    </div>

    <h1>Create Student Account</h1>
    <p>Register your details below</p>

    <?php if (!empty($message)): ?>
        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">

        <label for="first_name">First Name</label>
        <input
            type="text"
            id="first_name"
            name="first_name"
            placeholder="Enter first name"
            required
        >

        <label for="last_name">Last Name</label>
        <input
            type="text"
            id="last_name"
            name="last_name"
            placeholder="Enter last name"
            required
        >

        <label for="username">Username</label>
        <input
            type="text"
            id="username"
            name="username"
            placeholder="Enter username"
            required
        >

        <label for="email">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            placeholder="Enter email"
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

        <button type="submit">Create Account</button>

    </form>

    <p style="margin-top: 20px;">
        Already have an account?
        <a href="signin.php">Sign In</a>
    </p>

</div>

</body>
</html>