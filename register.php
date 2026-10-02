<?php

ob_start();

$conn = new mysqli("localhost", "root", "", "online-quiz");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    // Check password
    if ($password !== $confirm_password) {

        $message = "Password and Confirm Password do not match!";

    } else {

        // Check if email already exists
        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {

            $message = "Email already registered!";

        } else {

            // Hash password
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Insert user into database
            $sql = "INSERT INTO users (name, email, password, role)
                    VALUES (?, ?, ?, 'student')";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "sss",
                $name,
                $email,
                $hashed_password
            );

            if ($stmt->execute()) {

                // Registration successful
                // Go to login page
                header("Location: /project/login.php");
                exit();

            } else {

                $message = "Registration failed: " . $stmt->error;
            }

            $stmt->close();
        }

        $check->close();
    }
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - Online Quiz Management System</title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css?v=2">

</head>

<body class="register-body">

    <div class="register-page">

        <div class="register-container">

            <h1>Create Account</h1>

            <p class="register-subtitle">
                Register to start playing quizzes
            </p>

            <?php if ($message != "") { ?>

                <p style="color: red; text-align: center;">
                    <?php echo htmlspecialchars($message); ?>
                </p>

            <?php } ?>

            <form action="register.php" method="POST">

                <div class="form-group">

                    <label>Full Name</label>

                    <input
                        type="text"
                        name="name"
                        placeholder="Enter your full name"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Email Address</label>

                    <input
                        type="email"
                        name="email"
                        placeholder="Enter your email"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Password</label>

                    <input
                        type="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Confirm Password</label>

                    <input
                        type="password"
                        name="confirm_password"
                        placeholder="Confirm your password"
                        required
                    >

                </div>

                <button type="submit">
                    Create Account
                </button>

            </form>

            <p class="login-text">

                Already have an account?

                <a href="login.php">
                    Login here
                </a>

            </p>

        </div>

    </div>

</body>

</html>