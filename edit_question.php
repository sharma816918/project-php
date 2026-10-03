<?php

session_start();

// Check login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Check admin
if ($_SESSION["role"] !== "admin") {
    header("Location: dashboard.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "online-quiz");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check question ID
if (!isset($_GET["id"])) {
    header("Location: manage_questions.php");
    exit();
}

$id = intval($_GET["id"]);

// Update question
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $question = trim($_POST["question"]);
    $category = $_POST["category"];
    $option_a = trim($_POST["option_a"]);
    $option_b = trim($_POST["option_b"]);
    $option_c = trim($_POST["option_c"]);
    $option_d = trim($_POST["option_d"]);
    $correct_answer = $_POST["correct_answer"];

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

        header("Location: manage_questions.php");
        exit();

    } else {

        echo "Failed to update question.";

    }

    $stmt->close();
}

// Get question
$stmt = $conn->prepare(
    "SELECT * FROM questions WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows != 1) {

    echo "Question not found.";
    exit();

}

$row = $result->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Question</title>

</head>

<body>

    <h1>Edit Question</h1>

    <form method="POST">

        <label>Question</label><br>

        <textarea
            name="question"
            rows="4"
            cols="50"
            required
        ><?php echo htmlspecialchars($row["question"]); ?></textarea>

        <br><br>

        <label>Category</label><br>

        <select name="category" required>

            <option value="Python"
                <?php if ($row["category"] == "Python") echo "selected"; ?>>
                Python
            </option>

            <option value="PHP"
                <?php if ($row["category"] == "PHP") echo "selected"; ?>>
                PHP
            </option>

            <option value="SQL"
                <?php if ($row["category"] == "SQL") echo "selected"; ?>>
                SQL / MySQL
            </option>

            <option value="HTML"
                <?php if ($row["category"] == "HTML") echo "selected"; ?>>
                HTML
            </option>

            <option value="CSS"
                <?php if ($row["category"] == "CSS") echo "selected"; ?>>
                CSS
            </option>

            <option value="JavaScript"
                <?php if ($row["category"] == "JavaScript") echo "selected"; ?>>
                JavaScript
            </option>

            <option value="DBMS"
                <?php if ($row["category"] == "DBMS") echo "selected"; ?>>
                DBMS
            </option>

            <option value="Computer Networks"
                <?php if ($row["category"] == "Computer Networks") echo "selected"; ?>>
                Computer Networks
            </option>

            <option value="Operating System"
                <?php if ($row["category"] == "Operating System") echo "selected"; ?>>
                Operating System
            </option>

            <option value="Data Structures"
                <?php if ($row["category"] == "Data Structures") echo "selected"; ?>>
                Data Structures
            </option>

        </select>

        <br><br>

        <label>Option A</label><br>

        <input
            type="text"
            name="option_a"
            value="<?php echo htmlspecialchars($row["option_a"]); ?>"
            required
        >

        <br><br>

        <label>Option B</label><br>

        <input
            type="text"
            name="option_b"
            value="<?php echo htmlspecialchars($row["option_b"]); ?>"
            required
        >

        <br><br>

        <label>Option C</label><br>

        <input
            type="text"
            name="option_c"
            value="<?php echo htmlspecialchars($row["option_c"]); ?>"
            required
        >

        <br><br>

        <label>Option D</label><br>

        <input
            type="text"
            name="option_d"
            value="<?php echo htmlspecialchars($row["option_d"]); ?>"
            required
        >

        <br><br>

        <label>Correct Answer</label><br>

        <select name="correct_answer" required>

            <option value="A"
                <?php if ($row["correct_answer"] == "A") echo "selected"; ?>>
                Option A
            </option>

            <option value="B"
                <?php if ($row["correct_answer"] == "B") echo "selected"; ?>>
                Option B
            </option>

            <option value="C"
                <?php if ($row["correct_answer"] == "C") echo "selected"; ?>>
                Option C
            </option>

            <option value="D"
                <?php if ($row["correct_answer"] == "D") echo "selected"; ?>>
                Option D
            </option>

        </select>

        <br><br>

        <button type="submit">
            Update Question
        </button>

    </form>

    <br>

    <a href="manage_questions.php">
        Back to Manage Questions
    </a>

</body>

</html>

<?php

$conn->close();

?>