<?php

session_start();

$conn = new mysqli("localhost", "root", "", "online-quiz");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    // Find user from database
    $stmt = $conn->prepare(
        "SELECT id, name, email, password, role
         FROM users
         WHERE email = ?"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user["password"])) {

            // Create session
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role"] = $user["role"];

            // Check user role
            if ($user["role"] == "admin") {

                header("Location: admin_dashboard.php");
                exit();

            } else {

                header("Location: dashboard.php");
                exit();
            }

        } else {

            $message = "Invalid email or password!";
        }

    } else {

        $message = "Invalid email or password!";
    }

    $stmt->close();
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Online Quiz Management System</title>

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
    <link rel="stylesheet" href="css/style.css?v=10">

</head>

<body class="login-body">

    <div class="login-page">

        <div class="login-container">

            <h1>Welcome Back</h1>

            <p class="login-subtitle">
                Login to continue playing quizzes
            </p>

            <?php if ($message != "") { ?>

                <p style="color: red; text-align: center;">
                    <?php echo htmlspecialchars($message); ?>
                </p>

            <?php } ?>

            <form action="login.php" method="POST">

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

                <button type="submit">
                    Login
                </button>

            </form>

            <p class="register-text">

                Don't have an account?

                <a href="register.php">
                    Register here
                </a>

            </p>

        </div>

    </div>

</body>

</html>