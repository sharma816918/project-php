
<?php
session_start();

// Check login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Check admin role
if (($_SESSION["role"] ?? "") !== "admin") {
    header("Location: dashboard.php");
    exit();
}

$name = $_SESSION["name"] ?? "Admin";
$email = $_SESSION["email"] ?? "";
$role = $_SESSION["role"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard | Online Quiz Management System</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/style.css?v=33">

    <style>
        * {
            box-sizing: border-box;
        }

        body.admin-page {
            display: block !important;
            width: 100%;
            min-height: 100vh;
            margin: 0 !important;
            padding: 0 !important;
            background: #f4f6fc;
            font-family: 'Poppins', Arial, sans-serif;
            color: #202442;
        }

        .admin-header {
            width: 100%;
            background: linear-gradient(135deg, #4546c8, #7778f5);
            color: white;
            padding: 25px 6%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .admin-brand {
            color: white;
            font-size: 23px;
            font-weight: 800;
            margin: 0;
        }

        .admin-brand span {
            color: #b8f4ff;
        }

        .admin-header p {
            margin: 5px 0 0;
            color: #e9eaff;
            font-size: 13px;
        }

        .admin-logout {
            display: inline-block;
            padding: 10px 20px;
            border: 1px solid rgba(255,255,255,.6);
            border-radius: 10px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: .2s;
        }

        .admin-logout:hover {
            background: white;
            color: #4546c8;
        }

        .admin-main {
            width: 90%;
            max-width: 1150px;
            margin: 35px auto;
        }

        .admin-welcome {
            padding: 30px;
            border-radius: 20px;
            background: white;
            box-shadow: 0 10px 30px rgba(39,48,100,.07);
            margin-bottom: 28px;
        }

        .admin-welcome h1 {
            font-size: 30px;
            margin: 0 0 8px;
            color: #202442;
        }

        .admin-welcome p {
            color: #747b96;
            margin: 0;
            line-height: 1.7;
        }

        .admin-profile {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 35px;
        }

        .profile-card {
            padding: 22px;
            background: white;
            border: 1px solid #e4e8f2;
            border-radius: 15px;
            overflow-wrap: anywhere;
        }

        .profile-card .profile-label {
            color: #747b96;
            font-size: 13px;
            margin: 0 0 8px;
        }

        .profile-card .profile-value {
            font-size: 16px;
            font-weight: 700;
            color: #202442;
            margin: 0;
        }

        .admin-section-title {
            font-size: 23px;
            margin: 0 0 18px;
        }

        .admin-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px;
        }

        .admin-card {
            padding: 28px;
            background: white;
            border: 1px solid #e4e8f2;
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(39,48,100,.04);
            transition: transform .2s, box-shadow .2s;
        }

        .admin-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 30px rgba(39,48,100,.09);
        }

        .admin-icon {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: #eeeeff;
            font-size: 25px;
            margin-bottom: 18px;
        }

        .admin-card h3 {
            margin: 0 0 8px;
            font-size: 19px;
            color: #202442;
        }

        .admin-card p {
            color: #747b96;
            font-size: 14px;
            line-height: 1.7;
            min-height: 48px;
            margin: 0;
        }

        .admin-card a {
            display: inline-block;
            margin-top: 18px;
            padding: 11px 18px;
            border-radius: 9px;
            background: #5b5ce2;
            color: white;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: background .2s;
        }

        .admin-card a:hover {
            background: #4546c8;
            color: white;
        }

        .admin-footer {
            text-align: center;
            color: #747b96;
            font-size: 13px;
            padding: 25px 15px;
            margin-top: 30px;
        }

        @media (max-width: 700px) {
            .admin-profile,
            .admin-actions {
                grid-template-columns: 1fr;
            }

            .admin-welcome {
                padding: 23px;
            }

            .admin-welcome h1 {
                font-size: 24px;
            }

            .admin-header {
                padding: 22px 5%;
            }

            .admin-main {
                width: 92%;
            }

            .admin-card {
                padding: 23px;
            }
        }
    </style>
</head>

<body class="admin-page">

    <header class="admin-header">
        <div>
            <h2 class="admin-brand">Quiz<span>Master</span></h2>
            <p>Online Quiz Management System</p>
        </div>

        <a class="admin-logout"
           href="logout.php"
           onclick="return confirm('Are you sure you want to logout?');">
            Logout
        </a>
    </header>

    <main class="admin-main">

        <section class="admin-welcome">
            <h1>Welcome, <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>! 👋</h1>

            <p>
                Manage quiz questions, review student performance,
                and keep your quiz system organized from your administrator dashboard.
            </p>
        </section>

        <section class="admin-profile">

            <div class="profile-card">
                <p class="profile-label">Administrator Name</p>
                <p class="profile-value">
                    <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>

            <div class="profile-card">
                <p class="profile-label">Email Address</p>
                <p class="profile-value">
                    <?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>

            <div class="profile-card">
                <p class="profile-label">Account Role</p>
                <p class="profile-value">
                    <?= htmlspecialchars(ucfirst($role), ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>

        </section>

        <h2 class="admin-section-title">Quiz Management</h2>

        <section class="admin-actions">

            <article class="admin-card">
                <div class="admin-icon">➕</div>

                <h3>Add New Question</h3>

                <p>
                    Create a multiple-choice question, select its category,
                    and choose the correct answer.
                </p>

                <a href="add_question.php">Add Question →</a>
            </article>

            <article class="admin-card">
                <div class="admin-icon">📚</div>

                <h3>Manage Questions</h3>

                <p>
                    View existing questions, update their details,
                    or delete questions you no longer need.
                </p>

                <a href="manage_questions.php">Manage Questions →</a>
            </article>

            <article class="admin-card">
                <div class="admin-icon">📊</div>

                <h3>Student Results</h3>

                <p>
                    View all students' quiz attempts, categories,
                    correct and wrong answers, and percentage scores.
                </p>

                <a href="student_results.php">View Results →</a>
            </article>

            <article class="admin-card">
                <div class="admin-icon">🏠</div>

                <h3>Website Home</h3>

                <p>
                    Return to the main page of your Online Quiz
                    Management System.
                </p>

                <a href="index.php">Go to Home →</a>
            </article>

        </section>

    </main>

    <footer class="admin-footer">
        Online Quiz Management System &copy; <?= date("Y") ?>
    </footer>

</body>
</html>
