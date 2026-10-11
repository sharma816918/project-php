
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

$userId = (int)$_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT id, category, total_questions, correct_answers,
            wrong_answers, unanswered, percentage, created_at
     FROM results
     WHERE user_id = ?
     ORDER BY created_at DESC, id DESC"
);

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Quiz Results</title>
    <link rel="stylesheet" href="css/style.css?v=46">

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
            max-width: 1100px;
            width: 92%;
            margin: 30px auto;
        }

        .results-container {
            background: white;
            border-radius: 15px;
            padding: 24px;
            box-shadow: 0 5px 20px #0f172a0d;
        }

        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 750px;
        }

        th, td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            background: #eef2ff;
            color: #3730a3;
        }

        .percentage {
            font-weight: bold;
            color: #4f46e5;
        }

        .empty {
            text-align: center;
            padding: 35px 10px;
            color: #64748b;
        }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 22px;
        }

        .actions a {
            text-decoration: none;
            padding: 13px 20px;
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
    </style>
</head>

<body>

<header>
    <h1>My Quiz Results</h1>
    <p>View your previous quiz attempts and performance.</p>
</header>

<main>
    <section class="results-container">
        <h2>Quiz History</h2>

        <?php if ($result->num_rows > 0): ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Category</th>
                            <th>Score</th>
                            <th>Correct</th>
                            <th>Wrong</th>
                            <th>Unanswered</th>
                            <th>Percentage</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php $count = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo e($row["category"]); ?></td>
                            <td>
                                <?php echo (int)$row["correct_answers"]; ?>
                                /
                                <?php echo (int)$row["total_questions"]; ?>
                            </td>
                            <td><?php echo (int)$row["correct_answers"]; ?></td>
                            <td><?php echo (int)$row["wrong_answers"]; ?></td>
                            <td><?php echo (int)$row["unanswered"]; ?></td>
                            <td class="percentage">
                                <?php echo e($row["percentage"]); ?>%
                            </td>
                            <td>
                                <?php
                                echo e(date(
                                    "d M Y, h:i A",
                                    strtotime($row["created_at"])
                                ));
                                ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty">
                <h3>No quiz results yet!</h3>
                <p>Attempt a quiz to see your results here.</p>
            </div>
        <?php endif; ?>

        <div class="actions">
            <a class="primary" href="quiz.php">Start New Quiz</a>
            <a class="secondary" href="dashboard.php">Back to Dashboard</a>
        </div>
    </section>
</main>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
