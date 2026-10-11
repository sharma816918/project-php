
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (($_SESSION["role"] ?? "") === "admin") {
    header("Location: admin_dashboard.php");
    exit();
}

$categories = [
    "Python",
    "PHP",
    "SQL",
    "HTML",
    "CSS",
    "JavaScript",
    "DBMS",
    "Computer Networks",
    "Operating System",
    "Data Structures"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Quiz | Online Quiz</title>
    <link rel="stylesheet" href="css/style.css?v=42">

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: #f3f5fb;
            color: #1e293b;
        }

        .quiz-header {
            background: linear-gradient(120deg, #172554, #4f46e5);
            color: white;
            padding: 24px 6%;
        }

        .quiz-header h1 { margin: 0 0 8px; }
        .quiz-header p { margin: 0; color: #dbeafe; }

        .quiz-container {
            width: 92%;
            max-width: 650px;
            margin: 45px auto;
        }

        .quiz-card {
            background: white;
            border-radius: 18px;
            padding: 32px;
            box-shadow: 0 10px 35px #0f172a12;
        }

        .quiz-card h2 {
            margin-top: 0;
            margin-bottom: 10px;
        }

        .quiz-description {
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .quiz-label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .quiz-select {
            width: 100%;
            padding: 14px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background: white;
            font-size: 16px;
            margin-bottom: 22px;
        }

        .quiz-button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: #4f46e5;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .quiz-button:hover { background: #3730a3; }

        .quiz-back {
            display: inline-block;
            margin-top: 22px;
            color: #4f46e5;
            text-decoration: none;
            font-weight: bold;
        }

        @media (max-width: 500px) {
            .quiz-card { padding: 22px; }
            .quiz-container { margin: 25px auto; }
        }
    </style>
</head>

<body>

<header class="quiz-header">
    <h1>Choose Your Quiz</h1>
    <p>
        Welcome,
        <?php echo htmlspecialchars($_SESSION["name"] ?? "Student", ENT_QUOTES, "UTF-8"); ?>!
    </p>
</header>

<main class="quiz-container">
    <section class="quiz-card">
        <h2>Ready to Test Your Knowledge?</h2>
        <p class="quiz-description">
            Choose a subject below and start your quiz.
            Select the category you want to practise.
        </p>

        <form action="quiz_questions.php" method="GET">
            <label class="quiz-label" for="category">Select Quiz Category</label>

            <select class="quiz-select" name="category" id="category" required>
                <option value="">-- Select Category --</option>

                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo htmlspecialchars($category, ENT_QUOTES, "UTF-8"); ?>">
                        <?php echo $category === "SQL" ? "SQL / MySQL" : htmlspecialchars($category, ENT_QUOTES, "UTF-8"); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button class="quiz-button" type="submit">Start Quiz →</button>
        </form>

        <a class="quiz-back" href="dashboard.php">← Back to Dashboard</a>
    </section>
</main>

</body>
</html>
