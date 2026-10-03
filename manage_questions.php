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

// Get all questions
$sql = "SELECT * FROM questions ORDER BY id DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Questions</title>

</head>

<body>

    <h1>Manage Questions</h1>

    <p>
        <a href="add_question.php">
            + Add New Question
        </a>
    </p>

    <hr>

    <?php if ($result->num_rows > 0) { ?>

        <?php while ($row = $result->fetch_assoc()) { ?>

            <div>

                <h3>
                    <?php echo htmlspecialchars($row["question"]); ?>
                </h3>

                <p>
                    <strong>Category:</strong>
                    <?php echo htmlspecialchars($row["category"]); ?>
                </p>

                <p>
                    A:
                    <?php echo htmlspecialchars($row["option_a"]); ?>
                </p>

                <p>
                    B:
                    <?php echo htmlspecialchars($row["option_b"]); ?>
                </p>

                <p>
                    C:
                    <?php echo htmlspecialchars($row["option_c"]); ?>
                </p>

                <p>
                    D:
                    <?php echo htmlspecialchars($row["option_d"]); ?>
                </p>

                <p>
                    <strong>Correct Answer:</strong>
                    <?php echo htmlspecialchars($row["correct_answer"]); ?>
                </p>

                <!-- Edit Button -->

                <a href="edit_question.php?id=<?php echo $row["id"]; ?>">
                    Edit
                </a>

                &nbsp;

                <!-- Delete Button -->

                <a
                    href="delete_question.php?id=<?php echo $row["id"]; ?>"
                    onclick="return confirm('Are you sure you want to delete this question?');"
                >
                    Delete
                </a>

                <hr>

            </div>

        <?php } ?>

    <?php } else { ?>

        <p>No questions found.</p>

    <?php } ?>

    <br>

    <a href="admin_dashboard.php">
        Back to Admin Dashboard
    </a>

</body>

</html>

<?php

$conn->close();

?>