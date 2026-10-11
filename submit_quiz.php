
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

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: quiz.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "online-quiz");

if ($conn->connect_error) {
    die("Database connection failed.");
}

$conn->set_charset("utf8mb4");

$allowedCategories = [
    "Python", "PHP", "SQL", "HTML", "CSS", "JavaScript",
    "DBMS", "Computer Networks", "Operating System",
    "Data Structures"
];

$category = $_POST["category"] ?? "";
$answers = $_POST["answers"] ?? [];

if (!is_string($category) ||
    !in_array($category, $allowedCategories, true)) {
    $conn->close();
    header("Location: quiz.php");
    exit();
}

if (!is_array($answers)) {
    $answers = [];
}

$stmt = $conn->prepare(
    "SELECT id, question, option_a, option_b, option_c,
            option_d, correct_answer
     FROM questions
     WHERE category = ?
     ORDER BY id ASC"
);

$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
$correct = 0;
$review = [];

while ($q = $result->fetch_assoc()) {
    $total++;

    $id = (string)$q["id"];
    $selected = $answers[$id] ?? "";
    $validOptions = ["A", "B", "C", "D"];

    if (!is_string($selected) ||
        !in_array($selected, $validOptions, true)) {
        $selected = "";
    }

    $correctAnswer = strtoupper(trim((string)$q["correct_answer"]));

    $isCorrect = (
        $selected !== "" &&
        $selected === $correctAnswer
    );

    if ($isCorrect) {
        $correct++;
    }

    $review[] = [
        "question" => $q["question"],
        "selected" => $selected,
        "correct_answer" => $correctAnswer,
        "is_correct" => $isCorrect
    ];
}

$stmt->close();

if ($total === 0) {
    $conn->close();
    header("Location: quiz.php");
    exit();
}

$wrong = 0;
$unanswered = 0;

foreach ($review as $item) {
    if ($item["selected"] === "") {
        $unanswered++;
    } elseif (!$item["is_correct"]) {
        $wrong++;
    }
}

$percentage = round(($correct / $total) * 100, 2);

// Save the result for the logged-in student.
$userId = (int)$_SESSION["user_id"];

$saveStmt = $conn->prepare(
    "INSERT INTO results
    (user_id, category, total_questions, correct_answers,
     wrong_answers, unanswered, percentage)
    VALUES (?, ?, ?, ?, ?, ?, ?)"
);

if (!$saveStmt) {
    $conn->close();
    die("Could not prepare result saving query.");
}

$saveStmt->bind_param(
    "isiiiid",
    $userId,
    $category,
    $total,
    $correct,
    $wrong,
    $unanswered,
    $percentage
);

if (!$saveStmt->execute()) {
    $saveStmt->close();
    $conn->close();
    die("Result could not be saved. Please check your results table.");
}

$saveStmt->close();
$conn->close();

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$optionLabels = [
    "A" => "Option A",
    "B" => "Option B",
    "C" => "Option C",
    "D" => "Option D"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Result</title>
    <link rel="stylesheet" href="css/style.css?v=45">

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f5fb;
            color: #1e293b;
        }

        header {
            padding: 25px 6%;
            background: linear-gradient(120deg, #172554, #4f46e5);
            color: white;
        }

        header h1 { margin: 0 0 8px; }
        header p { margin: 0; color: #dbeafe; }

        main {
            max-width: 850px;
            width: 92%;
            margin: 30px auto;
        }

        .result-summary {
            padding: 30px;
            background: white;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 5px 20px #0f172a0d;
        }

        .saved-message {
            color: #15803d;
            font-weight: bold;
        }

        .score {
            font-size: 44px;
            font-weight: bold;
            color: #4f46e5;
            margin: 12px 0;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-top: 25px;
        }

        .stat {
            background: #f8fafc;
            border-radius: 10px;
            padding: 16px 8px;
        }

        .stat strong {
            display: block;
            font-size: 23px;
            margin-bottom: 6px;
        }

        .review-card {
            background: white;
            padding: 22px;
            border-radius: 13px;
            margin-top: 17px;
            border: 1px solid #e2e8f0;
        }

        .review-card h3 { line-height: 1.6; }

        .correct-text {
            color: #15803d;
            font-weight: bold;
        }

        .wrong-text {
            color: #b91c1c;
            font-weight: bold;
        }

        .result-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin: 25px 0;
        }

        .result-actions a {
            flex: 1;
            text-align: center;
            text-decoration: none;
            padding: 13px;
            border-radius: 9px;
            font-weight: bold;
        }

        .primary {
            background: #4f46e5;
            color: white;
        }

        .secondary {
            background: #e0e7ff;
            color: #3730a3;
        }

        @media (max-width: 600px) {
            .stats { grid-template-columns: repeat(2, 1fr); }
            .score { font-size: 36px; }
            .result-summary { padding: 22px; }
        }
    </style>
</head>

<body>

<header>
    <h1>Quiz Result</h1>
    <p>Category: <?php echo e($category); ?></p>
</header>

<main>
    <section class="result-summary">
        <h2>Quiz Completed!</h2>
        <p class="saved-message">✓ Your result has been saved successfully!</p>

        <div class="score">
            <?php echo $correct; ?> / <?php echo $total; ?>
        </div>

        <p>Percentage: <strong><?php echo e($percentage); ?>%</strong></p>

        <div class="stats">
            <div class="stat">
                <strong><?php echo $total; ?></strong>
                Total
            </div>

            <div class="stat">
                <strong class="correct-text"><?php echo $correct; ?></strong>
                Correct
            </div>

            <div class="stat">
                <strong class="wrong-text"><?php echo $wrong; ?></strong>
                Wrong
            </div>

            <div class="stat">
                <strong><?php echo $unanswered; ?></strong>
                Unanswered
            </div>
        </div>
    </section>

    <h2>Answer Review</h2>

    <?php foreach ($review as $index => $item): ?>
        <section class="review-card">
            <h3>
                Q<?php echo $index + 1; ?>.
                <?php echo e($item["question"]); ?>
            </h3>

            <p>
                Your answer:
                <?php
                echo $item["selected"] === ""
                    ? "Not answered"
                    : e($optionLabels[$item["selected"]]);
                ?>
            </p>

            <p>
                Correct answer:
                <strong>
                    <?php
                    echo e(
                        $optionLabels[$item["correct_answer"]]
                        ?? "Not available"
                    );
                    ?>
                </strong>
            </p>

            <p class="<?php echo $item["is_correct"] ? "correct-text" : "wrong-text"; ?>">
                <?php echo $item["is_correct"] ? "✓ Correct" : "✗ Incorrect"; ?>
            </p>
        </section>
    <?php endforeach; ?>

    <div class="result-actions">
        <a class="primary" href="quiz.php">Try Another Quiz</a>
        <a class="secondary" href="dashboard.php">Back to Dashboard</a>
    </div>
</main>

</body>
</html>
