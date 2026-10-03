<?php

session_start();

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Check if user is admin
if ($_SESSION["role"] !== "admin") {
    header("Location: dashboard.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard - Online Quiz Management System</title>

</head>

<body>

    <h1>Admin Dashboard</h1>

    <h2>
        Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?>!
    </h2>

    <p>You are logged in as an Administrator.</p>

    <p>
        Email:
        <?php echo htmlspecialchars($_SESSION["email"]); ?>
    </p>

    <p>
        Role:
        <?php echo htmlspecialchars($_SESSION["role"]); ?>
    </p>

    <hr>

    <h3>Quiz Management</h3>

    <p>
        <a href="add_question.php">
            Add New Question
        </a>
    </p>

    <p>
        <a href="#">
            Manage Questions
        </a>
    </p>

    <p>
        <a href="#">
            View Student Results
        </a>
    </p>

    <hr>

    <a href="logout.php">
        Logout
    </a>

</body>

</html>