
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (($_SESSION["role"] ?? "") !== "admin") {
    header("Location: dashboard.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "online-quiz");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

$sql = "SELECT
            r.id,
            u.name,
            u.email,
            r.category,
            r.total_questions,
            r.correct_answers,
            r.wrong_answers,
            r.unanswered,
            r.percentage,
            r.created_at
        FROM results r
        INNER JOIN users u ON r.user_id = u.id
        ORDER BY r.created_at DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Unable to load results: " . htmlspecialchars($conn->error));
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Results | QuizMaster</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f4f6fc;
            color: #202442;
            font-family: Arial, sans-serif;
        }

        header {
            background: linear-gradient(135deg, #4546c8, #7778f5);
            color: white;
            padding: 22px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        header h2 {
            margin: 0;
        }

        header a {
            color: white;
            text-decoration: none;
            border: 1px solid white;
            border-radius: 8px;
            padding: 10px 14px;
        }

        main {
            width: 94%;
            max-width: 1400px;
            margin: 30px auto;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 14px;
            box-shadow: 0 6px 24px #20244210;
            margin-bottom: 22px;
        }

        .card h1 {
            margin-top: 0;
        }

        .count {
            color: #4546c8;
            font-weight: bold;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 1000px;
            border-collapse: collapse;
        }

        th, td {
            padding: 13px 12px;
            border-bottom: 1px solid #e4e8f2;
            text-align: left;
            font-size: 13px;
            white-space: nowrap;
        }

        th {
            background: #eeeeff;
            color: #3637a0;
        }

        tbody tr:hover {
            background: #f8f9ff;
        }

        .percentage {
            color: #3730a3;
            font-weight: bold;
        }

        .empty {
            text-align: center;
            color: #747b96;
            padding: 30px 10px;
        }
    </style>
</head>

<body>

<header>
    <h2>QuizMaster — Student Results</h2>
    <a href="admin_dashboard.php">← Admin Dashboard</a>
</header>

<main>

    <section class="card">
        <h1>Student Results</h1>
        <p>View quiz performance and results for all students.</p>
        <p class="count">
            Total Quiz Attempts: <?= $result->num_rows ?>
        </p>
    </section>

    <section class="card">
        <?php if ($result->num_rows > 0): ?>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Category</th>
                            <th>Total</th>
                            <th>Correct</th>
                            <th>Wrong</th>
                            <th>Unanswered</th>
                            <th>Percentage</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $i = 1; ?>

                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= e($row["name"]) ?></td>
                                <td><?= e($row["email"]) ?></td>
                                <td><?= e($row["category"]) ?></td>
                                <td><?= e($row["total_questions"]) ?></td>
                                <td><?= e($row["correct_answers"]) ?></td>
                                <td><?= e($row["wrong_answers"]) ?></td>
                                <td><?= e($row["unanswered"]) ?></td>
                                <td class="percentage">
                                    <?= e($row["percentage"]) ?>%
                                </td>
                                <td><?= e($row["created_at"]) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <div class="empty">
                <h3>No Quiz Results Yet</h3>
                <p>Results will appear here after students submit quizzes.</p>
            </div>
        <?php endif; ?>
    </section>

</main>

</body>
</html>

<?php
$result->free();
$conn->close();
?>
