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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $question = trim($_POST["question"]);
    $category = $_POST["category"];
    $option_a = trim($_POST["option_a"]);
    $option_b = trim($_POST["option_b"]);
    $option_c = trim($_POST["option_c"]);
    $option_d = trim($_POST["option_d"]);
    $correct_answer = $_POST["correct_answer"];

    $sql = "INSERT INTO questions
            (question, category, option_a, option_b, option_c, option_d, correct_answer)
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
    } else {
        $message = "Failed to add question.";
    }

    $stmt->close();
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Question</title>

</head>

<body>

    <h1>Add Quiz Question</h1>

    <?php if ($message != "") { ?>

        <p>
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php } ?>

    <form method="POST">

        <label>Question</label><br>

        <textarea
            name="question"
            rows="4"
            cols="50"
            required
        ></textarea>

        <br><br>

        <label>Category</label><br>

        <select name="category" required>

            <option value="">Select Category</option>

            <option value="Python">Python</option>

            <option value="PHP">PHP</option>

            <option value="SQL">SQL / MySQL</option>

            <option value="HTML">HTML</option>

            <option value="CSS">CSS</option>

            <option value="JavaScript">JavaScript</option>

            <option value="DBMS">DBMS</option>

            <option value="Computer Networks">
                Computer Networks
            </option>

            <option value="Operating System">
                Operating System
            </option>

            <option value="Data Structures">
                Data Structures
            </option>

        </select>

        <br><br>

        <label>Option A</label><br>

        <input
            type="text"
            name="option_a"
            required
        >

        <br><br>

        <label>Option B</label><br>

        <input
            type="text"
            name="option_b"
            required
        >

        <br><br>

        <label>Option C</label><br>

        <input
            type="text"
            name="option_c"
            required
        >

        <br><br>

        <label>Option D</label><br>

        <input
            type="text"
            name="option_d"
            required
        >

        <br><br>

        <label>Correct Answer</label><br>

        <select name="correct_answer" required>

            <option value="">Select Correct Answer</option>

            <option value="A">Option A</option>

            <option value="B">Option B</option>

            <option value="C">Option C</option>

            <option value="D">Option D</option>

        </select>

        <br><br>

        <button type="submit">
            Add Question
        </button>

    </form>

    <br>

    <a href="admin_dashboard.php">
        Back to Admin Dashboard
    </a>

</body>

</html>