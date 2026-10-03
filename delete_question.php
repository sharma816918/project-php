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

// Delete question
$stmt = $conn->prepare(
    "DELETE FROM questions WHERE id = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$stmt->close();
$conn->close();

// Go back to manage questions
header("Location: manage_questions.php");
exit();

?>