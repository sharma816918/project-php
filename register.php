
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

    if ($name === "" || $email === "" || $password === "") {
        $message = "Please fill in all fields!";
    } elseif ($password !== $confirm_password) {
        $message = "Password and Confirm Password do not match!";
    } elseif (strlen($password) < 6) {
        $message = "Password must contain at least 6 characters!";
    } else {

        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {

            $message = "Email already registered!";

        } else {

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password, role)
                 VALUES (?, ?, ?, 'student')"
            );

            $stmt->bind_param(
                "sss",
                $name,
                $email,
                $hashed_password
            );

            if ($stmt->execute()) {
                $stmt->close();
                $check->close();
                $conn->close();

                header("Location: login.php");
                exit();
            } else {
                $message = "Registration failed. Please try again.";
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

    <title>Register | Online Quiz Management System</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
          rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 25px 15px;

            background:
                linear-gradient(
                    rgba(5, 18, 42, 0.65),
                    rgba(5, 18, 42, 0.65)
                ),
                url('image/background.jpg') center center / cover no-repeat fixed;
        }

        .register-container {
            width: 100%;
            max-width: 450px;
            padding: 35px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.3);
        }

        .logo {
            width: 65px;
            height: 65px;
            margin: 0 auto 15px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e7efff;
            font-size: 32px;
        }

        h1 {
            color: #142b52;
            font-size: 28px;
            text-align: center;
            margin-bottom: 8px;
        }

        .register-subtitle {
            color: #667085;
            font-size: 14px;
            text-align: center;
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            color: #243550;
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 7px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d5ddeb;
            border-radius: 10px;
            outline: none;
            font-size: 14px;
            background: white;
        }

        .form-group input:focus {
            border-color: #3168e0;
            box-shadow: 0 0 0 3px rgba(49, 104, 224, 0.12);
        }

        .register-btn {
            width: 100%;
            padding: 13px;
            margin-top: 5px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #2456d8, #4488f5);
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(36, 86, 216, 0.3);
        }

        .message {
            color: #b42318;
            background: #feeceb;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 16px;
            text-align: center;
            font-size: 13px;
        }

        .login-text {
            text-align: center;
            color: #667085;
            font-size: 14px;
            margin-top: 20px;
        }

        .login-text a {
            color: #2456d8;
            font-weight: 600;
            text-decoration: none;
        }

        .login-text a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .register-container {
                padding: 27px 22px;
            }

            h1 {
                font-size: 25px;
            }
        }
    </style>
</head>

<body>

    <div class="register-container">

        <div class="logo">🎓</div>

        <h1>Create Account</h1>

        <p class="register-subtitle">
            Register to start playing quizzes
        </p>

        <?php if ($message !== "") { ?>
            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php } ?>

        <form action="register.php" method="POST">

            <div class="form-group">
                <label for="name">Full Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Enter your full name"
                    required
                    value="<?php echo htmlspecialchars($_POST["name"] ?? ""); ?>"
                >
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                    value="<?php echo htmlspecialchars($_POST["email"] ?? ""); ?>"
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Create a password (minimum 6 characters)"
                    minlength="6"
                    required
                >
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Confirm your password"
                    minlength="6"
                    required
                >
            </div>

            <button type="submit" class="register-btn">
                Create Account
            </button>

        </form>

        <p class="login-text">
            Already have an account?
            <a href="login.php">Login here</a>
        </p>

    </div>

</body>
</html>
