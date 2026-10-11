
<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION["role"] !== "admin") {
    header("Location: dashboard.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "online-quiz");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $question = trim($_POST["question"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $option_a = trim($_POST["option_a"] ?? "");
    $option_b = trim($_POST["option_b"] ?? "");
    $option_c = trim($_POST["option_c"] ?? "");
    $option_d = trim($_POST["option_d"] ?? "");
    $correct_answer = $_POST["correct_answer"] ?? "";

    $allowed_categories = [
        "Python", "PHP", "SQL", "HTML", "CSS",
        "JavaScript", "DBMS", "Computer Networks",
        "Operating System", "Data Structures"
    ];

    if (
        $question === "" ||
        !in_array($category, $allowed_categories, true) ||
        $option_a === "" ||
        $option_b === "" ||
        $option_c === "" ||
        $option_d === "" ||
        !in_array($correct_answer, ["A", "B", "C", "D"], true)
    ) {
        $message = "Please fill in all fields correctly.";
        $message_type = "error";
    } else {

        $sql = "INSERT INTO questions
                (question, category, option_a, option_b,
                 option_c, option_d, correct_answer)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "sssssss",
            $question,
            $category,
            $option_a,
            $option_b,
            $option_c,
            $option_d,
            $correct_answer
        );

        if ($stmt->execute()) {
            $message = "Question added successfully!";
            $message_type = "success";
        } else {
            $message = "Failed to add question. Please try again.";
            $message_type = "error";
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

    <title>Add Question | QuizMaster</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/style.css?v=40">

    <style>
        body.add-question-page {
            display: block;
            padding: 0;
            background: #f4f6fc;
        }

        .aq-header {
            padding: 22px 6%;
            background: linear-gradient(135deg, #4546c8, #7778f5);
            color: white;
        }

        .aq-header h2 {
            color: white;
            margin: 0;
            font-size: 23px;
        }

        .aq-header p {
            color: #e9eaff;
            margin: 5px 0 0;
            font-size: 13px;
        }

        .aq-main {
            width: 90%;
            max-width: 850px;
            margin: 35px auto;
        }

        .aq-heading {
            margin-bottom: 22px;
        }

        .aq-heading h1 {
            font-size: 30px;
            margin-bottom: 7px;
        }

        .aq-heading p {
            color: #747b96;
        }

        .aq-card {
            padding: 32px;
            background: white;
            border: 1px solid #e4e8f2;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(39,48,100,.07);
        }

        .aq-form-group {
            margin-bottom: 21px;
        }

        .aq-form-group label {
            display: block;
            margin-bottom: 8px;
            color: #414866;
            font-weight: 600;
            font-size: 14px;
        }

        .aq-form-group input,
        .aq-form-group select,
        .aq-form-group textarea {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #dce1ee;
            border-radius: 10px;
            background: #fff;
            font: inherit;
            font-size: 14px;
            outline: none;
        }

        .aq-form-group input:focus,
        .aq-form-group select:focus,
        .aq-form-group textarea:focus {
            border-color: #5b5ce2;
            box-shadow: 0 0 0 3px rgba(91,92,226,.12);
        }

        .aq-form-group textarea {
            min-height: 110px;
            resize: vertical;
        }

        .aq-options-title {
            margin: 28px 0 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e4e8f2;
            font-size: 19px;
        }

        .aq-options-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0 18px;
        }

        .aq-submit {
            width: 100%;
            padding: 14px 20px;
            border: 0;
            border-radius: 10px;
            background: #5b5ce2;
            color: white;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            transition: .2s;
        }

        .aq-submit:hover {
            background: #4546c8;
            transform: translateY(-1px);
        }

        .aq-message {
            padding: 13px 16px;
            border-radius: 10px;
            margin-bottom: 22px;
            font-size: 14px;
        }

        .aq-message.success {
            background: #e9fbf2;
            color: #087b49;
            border: 1px solid #bcebd1;
        }

        .aq-message.error {
            background: #fff0f0;
            color: #b4232b;
            border: 1px solid #ffd0d0;
        }

        .aq-back {
            display: inline-block;
            margin-top: 22px;
            font-weight: 600;
        }

        .aq-footer {
            text-align: center;
            padding: 20px;
            color: #747b96;
            font-size: 13px;
        }

        @media (max-width: 600px) {
            .aq-card {
                padding: 22px 18px;
            }

            .aq-options-grid {
                grid-template-columns: 1fr;
            }

            .aq-heading h1 {
                font-size: 25px;
            }
        }
    </style>
</head>

<body class="add-question-page">

    <header class="aq-header">
        <h2>QuizMaster</h2>
        <p>Online Quiz Management System · Admin Panel</p>
    </header>

    <main class="aq-main">

        <div class="aq-heading">
            <h1>Add New Question</h1>
            <p>Create a multiple-choice question for your students.</p>
        </div>

        <section class="aq-card">

            <?php if ($message !== "") { ?>
                <div class="aq-message <?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php } ?>

            <form action="add_question.php" method="POST">

                <div class="aq-form-group">
                    <label for="question">Question</label>
                    <textarea
                        id="question"
                        name="question"
                        placeholder="Enter your question here..."
                        required
                    ><?php echo htmlspecialchars($_POST["question"] ?? ""); ?></textarea>
                </div>

                <div class="aq-form-group">
                    <label for="category">Quiz Category</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <?php
                        $categories = [
                            "Python", "PHP", "SQL", "HTML", "CSS",
                            "JavaScript", "DBMS", "Computer Networks",
                            "Operating System", "Data Structures"
                        ];

                        foreach ($categories as $cat) {
                            $selected = (($_POST["category"] ?? "") === $cat)
                                ? "selected"
                                : "";

                            echo '<option value="' .
                                htmlspecialchars($cat) . '" ' .
                                $selected . '>' .
                                htmlspecialchars($cat) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <h3 class="aq-options-title">Answer Options</h3>

                <div class="aq-options-grid">

                    <div class="aq-form-group">
                        <label for="option_a">Option A</label>
                        <input id="option_a" type="text" name="option_a"
                            placeholder="Enter option A"
                            value="<?php echo htmlspecialchars($_POST["option_a"] ?? ""); ?>"
                            required>
                    </div>

                    <div class="aq-form-group">
                        <label for="option_b">Option B</label>
                        <input id="option_b" type="text" name="option_b"
                            placeholder="Enter option B"
                            value="<?php echo htmlspecialchars($_POST["option_b"] ?? ""); ?>"
                            required>
                    </div>

                    <div class="aq-form-group">
                        <label for="option_c">Option C</label>
                        <input id="option_c" type="text" name="option_c"
                            placeholder="Enter option C"
                            value="<?php echo htmlspecialchars($_POST["option_c"] ?? ""); ?>"
                            required>
                    </div>

                    <div class="aq-form-group">
                        <label for="option_d">Option D</label>
                        <input id="option_d" type="text" name="option_d"
                            placeholder="Enter option D"
                            value="<?php echo htmlspecialchars($_POST["option_d"] ?? ""); ?>"
                            required>
                    </div>

                </div>

                <div class="aq-form-group">
                    <label for="correct_answer">Correct Answer</label>
                    <select id="correct_answer" name="correct_answer" required>
                        <option value="">Select Correct Answer</option>
                        <?php foreach (["A", "B", "C", "D"] as $letter) {
                            $selected = (($_POST["correct_answer"] ?? "") === $letter)
                                ? "selected"
                                : "";
                        ?>
                            <option value="<?php echo $letter; ?>" <?php echo $selected; ?>>
                                Option <?php echo $letter; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <button class="aq-submit" type="submit">
                    + Add Question
                </button>

            </form>

        </section>

        <a class="aq-back" href="admin_dashboard.php">
            ← Back to Admin Dashboard
        </a>

    </main>

    <footer class="aq-footer">
        Online Quiz Management System &copy; <?php echo date("Y"); ?>
    </footer>

</body>
</html>
