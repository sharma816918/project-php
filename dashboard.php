<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>

<body>

    <h1>Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?>!</h1>

    <p>You have successfully logged in.</p>

    <p>
        Email:
        <?php echo htmlspecialchars($_SESSION["email"]); ?>
    </p>

    <p>
        Role:
        <?php echo htmlspecialchars($_SESSION["role"]); ?>
    </p>

    <a href="logout.php">Logout</a>

</body>
</html>