
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    echo "Password: " . $password . "<br>";
    echo "Confirm Password: " . $confirm_password . "<br>";

    if ($password !== $confirm_password) {
        echo "Password and Confirm Password do not match!";
    } else {
        echo "Password matched!";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - Online Quiz Management System</title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

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

            <form action="" method="POST">

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
                <a href="login.php">Login here</a>
            </p>

        </div>

    </div>

</body>
</html>