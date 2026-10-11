
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

    if (empty($email) || empty($password)) {
        $message = "Please enter email and password!";
    } else {

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

            if (password_verify($password, $user["password"])) {

                session_regenerate_id(true);

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["email"] = $user["email"];
                $_SESSION["role"] = $user["role"];

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
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | Online Quiz Management System</title>

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
            padding: 20px;

            background:
                linear-gradient(
                    rgba(5, 18, 42, 0.65),
                    rgba(5, 18, 42, 0.65)
                ),
                url('image/background.jpg') center center / cover no-repeat fixed;
        }

        .login-container {
            width: 100%;
            max-width: 430px;
            padding: 40px 35px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .logo {
            width: 75px;
            height: 75px;
            margin: 0 auto 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e7efff;
            font-size: 36px;
        }

        h1 {
            color: #142b52;
            font-size: 29px;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #667085;
            font-size: 14px;
            margin-bottom: 28px;
        }

        .input-group {
            text-align: left;
            margin-bottom: 18px;
        }

        .input-group label {
            display: block;
            color: #243550;
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 7px;
        }

        .input-group input {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #d5ddeb;
            border-radius: 10px;
            outline: none;
            font-size: 14px;
            background: #fff;
        }

        .input-group input:focus {
            border-color: #3168e0;
            box-shadow: 0 0 0 3px rgba(49, 104, 224, 0.12);
        }

        .login-btn {
            width: 100%;
            padding: 14px;
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

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(36, 86, 216, 0.3);
        }

        .message {
            padding: 11px;
            margin-bottom: 18px;
            border-radius: 8px;
            color: #b42318;
            background: #feeceb;
            font-size: 13px;
        }

        .register-link {
            margin-top: 22px;
            color: #667085;
            font-size: 14px;
        }

        .register-link a {
            color: #2456d8;
            font-weight: 600;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 22px;
            }

            h1 {
                font-size: 25px;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">

        <div class="logo">🎓</div>

        <h1>Welcome Back!</h1>

        <p class="subtitle">
            Login to continue playing quizzes
        </p>

        <?php if (!empty($message)) { ?>
            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php } ?>

        <form action="login.php" method="POST">

            <div class="input-group">
                <label for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >
            </div>

            <button type="submit" class="login-btn">
                Login
            </button>

        </form>

        <p class="register-link">
            Don't have an account?
            <a href="register.php">Register Now</a>
        </p>

    </div>

</body>
</html>
