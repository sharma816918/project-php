
<?php
session_start();

// Check login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Check admin
if (($_SESSION["role"] ?? "") !== "admin") {
    header("Location: dashboard.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "online-quiz");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Get question ID from URL or submitted form
$id = (int)($_POST["id"] ?? $_GET["id"] ?? 0);

if ($id <= 0) {
    header("Location: manage_questions.php");
    exit();
}

$message = "";

// Update question
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $question = trim($_POST["question"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $option_a = trim($_POST["option_a"] ?? "");
    $option_b = trim($_POST["option_b"] ?? "");
    $option_c = trim($_POST["option_c"] ?? "");
    $option_d = trim($_POST["option_d"] ?? "");
    $correct_answer = strtoupper(trim($_POST["correct_answer"] ?? ""));

    $categories = [
        "Python", "PHP", "SQL", "HTML", "CSS",
        "JavaScript", "DBMS", "Computer Networks",
        "Operating System", "Data Structures"
    ];

    if (
        $question === "" ||
        $option_a === "" ||
        $option_b === "" ||
        $option_c === "" ||
        $option_d === "" ||
        !in_array($category, $categories, true) ||
        !in_array($correct_answer, ["A", "B", "C", "D"], true)
    ) {
        $message = "Please fill in all fields correctly.";
    } else {
        $sql = "UPDATE questions
                SET question = ?,
                    category = ?,
                    option_a = ?,
                    option_b = ?,
                    option_c = ?,
                    option_d = ?,
                    correct_answer = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "sssssssi",
            $question,
            $category,
            $option_a,
            $option_b,
            $option_c,
            $option_d,
            $correct_answer,
            $id
        );

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();

            header("Location: manage_questions.php");
            exit();
        } else {
            $message = "Failed to update question. Please try again.";
            $stmt->close();
        }
    }
}

// Fetch question details
$stmt = $conn->prepare("SELECT * FROM questions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$stmt->close();

if (!$row) {
    $conn->close();
    die("Question not found. <a href='manage_questions.php'>Go Back</a>");
}

// Escape displayed values
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$categories = [
    "Python", "PHP", "SQL", "HTML", "CSS",
    "JavaScript", "DBMS", "Computer Networks",
    "Operating System", "Data Structures"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Question | Online Quiz System</title>

    <link rel="stylesheet" href="css/style.css">

    <style>
        /* Fix existing stylesheet layout conflicts */
        html {
            min-height: 100%;
        }

        body {
            display: block !important;
            width: 100% !important;
            min-height: 100vh;
            margin: 0 !important;
            padding: 0 !important;
            font-family: Arial, Helvetica, sans-serif;
            background: #f1f5ff;
            color: #1e293b;
        }

        .topbar {
            display: flex !important;
            position: static !important;
            float: none !important;
            width: 100% !important;
            min-height: 70px;
            margin: 0 !important;
            padding: 18px 5% !important;
            background: #172554;
            color: white;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }

        .topbar h2 {
            margin: 0;
            color: white;
            font-size: 22px;
        }

        .back-link {
            display: inline-block;
            background: #334780;
            color: white;
            padding: 11px 16px;
            border-radius: 7px;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            background: #475b8c;
        }

        .container {
            display: block !important;
            float: none !important;
            position: static !important;
            width: min(760px, 92%) !important;
            max-width: 760px;
            margin: 35px auto !important;
            padding: 0 !important;
        }

        .card {
            display: block !important;
            float: none !important;
            position: static !important;
            width: 100% !important;
            margin: 0 auto !important;
            padding: 30px;
            background: white;
            border-radius: 14px;
            box-shadow: 0 8px 28px rgba(15, 23, 42, 0.08);
        }

        .card h1 {
            margin: 0 0 8px;
            color: #172554;
            text-align: center;
            font-size: 28px;
        }

        .subtitle {
            margin: 0 0 25px;
            color: #64748b;
            text-align: center;
            line-height: 1.5;
        }

        .message {
            margin-bottom: 20px;
            padding: 12px 15px;
            background: #fee2e2;
            color: #991b1b;
            border-radius: 8px;
        }

        .form-group {
            display: block;
            width: 100%;
            margin-bottom: 19px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #334155;
            font-size: 14px;
            font-weight: 600;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            display: block;
            width: 100%;
            max-width: 100%;
            padding: 12px 13px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: white;
            color: #1e293b;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 15px;
            outline: none;
            box-sizing: border-box;
        }

        .form-group textarea {
            min-height: 105px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .options-grid {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0 18px;
            width: 100%;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 25px;
        }

        .btn {
            display: inline-block;
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
        }

        .btn-save {
            background: #2563eb;
            color: white;
        }

        .btn-save:hover {
            background: #1d4ed8;
        }

        .btn-cancel {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-cancel:hover {
            background: #cbd5e1;
        }

        @media (max-width: 600px) {
            .topbar {
                padding: 16px 4% !important;
            }

            .topbar h2 {
                font-size: 19px;
            }

            .container {
                width: 94% !important;
                margin: 22px auto !important;
            }

            .card {
                padding: 22px 17px;
            }

            .options-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .card h1 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

<header class="topbar">
    <h2>Online Quiz Management System</h2>

    <a class="back-link" href="manage_questions.php">
        &larr; Back to Questions
    </a>
</header>

<main class="container">
    <section class="card">

        <h1>Edit Question</h1>

        <p class="subtitle">
            Update the question details and save your changes.
        </p>

        <?php if ($message !== ""): ?>
            <div class="message"><?= e($message) ?></div>
        <?php endif; ?>

        <form method="POST" action="edit_question.php">
            <input type="hidden" name="id" value="<?= e($id) ?>">

            <div class="form-group">
                <label for="question">Question</label>

                <textarea
                    id="question"
                    name="question"
                    required
                ><?= e($row["question"]) ?></textarea>
            </div>

            <div class="form-group">
                <label for="category">Category</label>

                <select id="category" name="category" required>
                    <?php foreach ($categories as $cat): ?>
                        <option
                            value="<?= e($cat) ?>"
                            <?= $row["category"] === $cat ? "selected" : "" ?>
                        >
                            <?= e($cat) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="options-grid">
                <div class="form-group">
                    <label for="option_a">Option A</label>
                    <input
                        type="text"
                        id="option_a"
                        name="option_a"
                        value="<?= e($row["option_a"]) ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="option_b">Option B</label>
                    <input
                        type="text"
                        id="option_b"
                        name="option_b"
                        value="<?= e($row["option_b"]) ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="option_c">Option C</label>
                    <input
                        type="text"
                        id="option_c"
                        name="option_c"
                        value="<?= e($row["option_c"]) ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="option_d">Option D</label>
                    <input
                        type="text"
                        id="option_d"
                        name="option_d"
                        value="<?= e($row["option_d"]) ?>"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="correct_answer">Correct Answer</label>

                <select
                    id="correct_answer"
                    name="correct_answer"
                    required
                >
                    <?php foreach (["A", "B", "C", "D"] as $answer): ?>
                        <option
                            value="<?= $answer ?>"
                            <?= $row["correct_answer"] === $answer ? "selected" : "" ?>
                        >
                            Option <?= $answer ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-save">
                    Update Question
                </button>

                <a href="manage_questions.php" class="btn btn-cancel">
                    Cancel
                </a>
            </div>
        </form>

    </section>
</main>

</body>
</html>

<?php
$conn->close();
?>