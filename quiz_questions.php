```php
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

$conn = new mysqli("localhost", "root", "", "online-quiz");

if ($conn->connect_error) {
    die("Database connection failed.");
}

$conn->set_charset("utf8mb4");

$categories = [
    "Python", "PHP", "SQL", "HTML", "CSS", "JavaScript",
    "DBMS", "Computer Networks", "Operating System",
    "Data Structures"
];

$category = trim($_GET["category"] ?? "");

if (!in_array($category, $categories, true)) {
    header("Location: quiz.php");
    exit();
}

$stmt = $conn->prepare(
    "SELECT id, question, option_a, option_b, option_c, option_d
     FROM questions
     WHERE category = ?
     ORDER BY id ASC"
);

$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];

while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}

$stmt->close();
$conn->close();

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($category); ?> Quiz</title>
    <link rel="stylesheet" href="css/style.css?v=43">

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f5fb;
            color: #1e293b;
        }

        header {
            padding: 24px 6%;
            color: white;
            background: linear-gradient(120deg, #172554, #4f46e5);
        }

        header h1 { margin: 0 0 8px; }
        header p { margin: 0; color: #dbeafe; }

        main {
            max-width: 850px;
            width: 92%;
            margin: 30px auto;
        }

        .question-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 18px;
            box-shadow: 0 5px 18px #0f172a0a;
        }

        .question-card h3 {
            line-height: 1.6;
            margin-top: 0;
        }

        .option {
            display: block;
            padding: 13px;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            margin: 10px 0;
            cursor: pointer;
            overflow-wrap: anywhere;
        }

        .option:hover {
            background: #eef2ff;
            border-color: #818cf8;
        }

        .option input {
            margin-right: 10px;
        }

        .submit-btn {
            display: block;
            width: 100%;
            border: none;
            border-radius: 10px;
            padding: 15px;
            background: #4f46e5;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .submit-btn:hover { background: #3730a3; }

        .back-link {
            display: inline-block;
            margin: 0 0 24px;
            color: #4f46e5;
            text-decoration: none;
            font-weight: bold;
        }

        .empty-card {
            background: white;
            border-radius: 14px;
            padding: 30px;
            text-align: center;
        }

        @media (max-width: 550px) {
            .question-card { padding: 17px; }
        }
    </style>
</head>

<body>

<header>
    <h1><?php echo e($category); ?> Quiz</h1>
    <p>Answer the questions and submit your quiz.</p>
</header>

<main>
    <a class="back-link" href="quiz.php">← Choose Another Category</a>

    <?php if (count($questions) === 0): ?>

        <section class="empty-card">
            <h2>No Questions Available</h2>
            <p>Admin has not added questions for this category yet.</p>
            <a href="quiz.php">Choose another category</a>
        </section>

    <?php else: ?>

        <p>Total Questions: <strong><?php echo count($questions); ?></strong></p>

        <form action="submit_quiz.php" method="POST">
            <input type="hidden" name="category" value="<?php echo e($category); ?>">

            <?php foreach ($questions as $index => $q): ?>
                <section class="question-card">
                    <h3>
                        Q<?php echo $index + 1; ?>.
                        <?php echo e($q["question"]); ?>
                    </h3>

                    <?php
                    $options = [
                        "A" => $q["option_a"],
                        "B" => $q["option_b"],
                        "C" => $q["option_c"],
                        "D" => $q["option_d"]
                    ];
                    ?>

                    <?php foreach ($options as $key => $option): ?>
                        <label class="option">
                            <input
                                type="radio"
                                name="answers[<?php echo (int)$q["id"]; ?>]"
                                value="<?php echo $key; ?>"
                            >
                            <strong><?php echo $key; ?>.</strong>
                            <?php echo e($option); ?>
                        </label>
                    <?php endforeach; ?>
                </section>
            <?php endforeach; ?>

            <button class="submit-btn" type="submit"
                    onclick="return confirm('Are you ready to submit your quiz?');">
                Submit Quiz
            </button>
        </form>

    <?php endif; ?>
</main>

</body>
</html>
```