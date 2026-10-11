
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (($_SESSION["role"] ?? "student") === "admin") {
    header("Location: admin_dashboard.php");
    exit();
}

$name = $_SESSION["name"] ?? "Student";
$email = $_SESSION["email"] ?? "";
$role = $_SESSION["role"] ?? "student";

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | QuizMaster</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f4f6fc;
            color: #1e293b;
            font-family: 'Poppins', Arial, sans-serif;
        }

        .header {
            padding: 22px 6%;
            background: linear-gradient(135deg, #4546c8, #7778f5);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .brand {
            color: white;
            font-size: 25px;
            font-weight: 800;
            margin: 0;
        }

        .brand span {
            color: #b8f4ff;
        }

        .header p {
            color: #e9eaff;
            font-size: 13px;
            margin: 4px 0 0;
        }

        .main {
            width: 90%;
            max-width: 1150px;
            margin: 35px auto;
        }

        .welcome {
            padding: 32px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(39, 48, 100, 0.07);
        }

        .welcome h1 {
            margin: 0 0 10px;
            font-size: 29px;
        }

        .welcome p {
            margin: 0;
            color: #747b96;
            line-height: 1.7;
        }

        .section-title {
            margin: 30px 0 18px;
            font-size: 23px;
        }

        .profile {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .profile-card {
            padding: 22px;
            background: white;
            border: 1px solid #e4e8f2;
            border-radius: 15px;
            overflow-wrap: anywhere;
        }

        .profile-card .label {
            color: #747b96;
            font-size: 13px;
            margin: 0 0 8px;
        }

        .profile-card .value {
            font-size: 16px;
            font-weight: 700;
            margin: 0;
        }

        .actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .card {
            position: relative;
            padding: 26px;
            background: white;
            border: 1px solid #e4e8f2;
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(39, 48, 100, 0.04);
        }

        .card h3 {
            margin: 0 0 8px;
            font-size: 20px;
        }

        .card p {
            color: #747b96;
            font-size: 14px;
            line-height: 1.7;
            min-height: 48px;
        }

        .icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            margin-bottom: 18px;
            background: #eeeeff;
            border-radius: 14px;
            font-size: 25px;
        }

        .button {
            display: inline-block;
            position: relative;
            z-index: 10;
            padding: 12px 20px;
            background: #5b5ce2;
            color: white;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            pointer-events: auto;
        }

        .button:hover {
            background: #4546c8;
            color: white;
        }

        .button.secondary {
            background: #eeeeff;
            color: #5556d9;
        }

        .button.secondary:hover {
            background: #dedfff;
        }

        .footer {
            padding: 25px 15px;
            color: #747b96;
            font-size: 13px;
            text-align: center;
        }

        @media (max-width: 700px) {
            .profile,
            .actions {
                grid-template-columns: 1fr;
            }

            .welcome {
                padding: 24px;
            }

            .welcome h1 {
                font-size: 23px;
            }
        }
    </style>
</head>

<body>

<header class="header">
    <div>
        <h2 class="brand">Quiz<span>Master</span></h2>
        <p>Online Quiz Management System</p>
    </div>

    <a class="button secondary"
       href="/project/logout.php"
       onclick="return confirm('Are you sure you want to logout?');">
        Logout
    </a>
</header>

<main class="main">

    <section class="welcome">
        <h1>Welcome, <?php echo e($name); ?>! 👋</h1>
        <p>
            Ready to test your knowledge? Choose a quiz category
            and start improving your skills.
        </p>
    </section>

    <h2 class="section-title">Your Profile</h2>

    <section class="profile">
        <div class="profile-card">
            <p class="label">Student Name</p>
            <p class="value"><?php echo e($name); ?></p>
        </div>

        <div class="profile-card">
            <p class="label">Email Address</p>
            <p class="value"><?php echo e($email); ?></p>
        </div>

        <div class="profile-card">
            <p class="label">Account Role</p>
            <p class="value"><?php echo e(ucfirst($role)); ?></p>
        </div>
    </section>

    <h2 class="section-title">What Would You Like To Do?</h2>

    <section class="actions">

        <article class="card">
            <div class="icon">📝</div>
            <h3>Start a Quiz</h3>
            <p>
                Select a subject and answer multiple-choice questions
                to test your knowledge.
            </p>
            <a class="button" href="/project/quiz.php">
                Start Quiz →
            </a>
        </article>

        <article class="card">
            <div class="icon">📊</div>
            <h3>My Results</h3>
            <p>
                View your previous quiz attempts, scores,
                percentages, and quiz history.
            </p>
            <a class="button" href="/project/my_results.php">
                My Results →
            </a>
        </article>

        <article class="card">
            <div class="icon">👤</div>
            <h3>Profile</h3>
            <p>
                View your registered name, email address,
                and student account information.
            </p>
            <a class="button secondary" href="/project/dashboard.php">
                View Profile
            </a>
        </article>

        <article class="card">
            <div class="icon">🚪</div>
            <h3>Logout</h3>
            <p>
                Securely log out of your account when
                you have finished using the quiz system.
            </p>
            <a class="button secondary"
               href="/project/logout.php"
               onclick="return confirm('Are you sure you want to logout?');">
                Logout
            </a>
        </article>

    </section>

</main>

<footer class="footer">
    Online Quiz Management System &copy; <?php echo date("Y"); ?>
</footer>

</body>
</html>
