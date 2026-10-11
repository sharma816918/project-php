
<?php
session_start();

/* DATABASE CONNECTION */
$conn = new mysqli("localhost", "root", "", "online-quiz");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

/* ADMIN CHECK */
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: login.php");
    exit();
}

/* CATEGORIES */
$categories = [
    'Python',
    'PHP',
    'SQL',
    'HTML',
    'CSS',
    'JavaScript',
    'DBMS',
    'Computer Networks',
    'Operating System',
    'Data Structures'
];

/* CATEGORY FILTER */
$category = $_GET['category'] ?? '';

if ($category !== '' && !in_array($category, $categories, true)) {
    $category = '';
}

/* FETCH QUESTIONS */
if ($category !== '') {
    $stmt = $conn->prepare(
        "SELECT * FROM questions WHERE category = ? ORDER BY id ASC"
    );
    $stmt->bind_param("s", $category);
} else {
    $stmt = $conn->prepare(
        "SELECT * FROM questions ORDER BY id ASC"
    );
}

if (!$stmt) {
    die("Query error: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();
$totalQuestions = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions</title>

    <style>
        * {
            box-sizing: border-box;
        }

        html {
            overflow-y: scroll !important;
            height: auto !important;
        }

        body {
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            height: auto !important;
            max-height: none !important;
            overflow: visible !important;
            overflow-y: auto !important;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #222;
        }

        .container {
            width: 100%;
            max-width: 1250px;
            margin: 0 auto;
            padding-bottom: 50px;
            height: auto !important;
            max-height: none !important;
            overflow: visible !important;
        }

        h1 {
            color: #243b55;
        }

        .filter-box {
            padding: 20px;
            margin: 20px 0;
            background: white;
            border-radius: 8px;
        }

        select,
        button {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
        }

        select {
            min-width: 180px;
            border: 1px solid #ccc;
        }

        button {
            border: none;
            color: white;
            background: #1769aa;
            cursor: pointer;
        }

        a {
            text-decoration: none;
        }

        .reset,
        .back,
        .edit,
        .delete {
            display: inline-block;
            padding: 9px 12px;
            margin: 3px;
            color: white;
            border-radius: 5px;
        }

        .reset {
            background: #526579;
        }

        .back {
            background: #243b55;
        }

        .edit {
            background: #1769aa;
        }

        .delete {
            background: #c62828;
        }

        .count {
            font-weight: bold;
            margin: 20px 0;
        }

        .table-wrap {
            width: 100%;
            height: auto !important;
            max-height: none !important;
            overflow-x: auto;
            overflow-y: visible !important;
            background: white;
            border-radius: 8px;
        }

        table {
            width: 100%;
            min-width: 850px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #243b55;
            color: white;
        }

        tr:nth-child(even) {
            background: #f8fafc;
        }

        .question {
            min-width: 280px;
            overflow-wrap: anywhere;
        }

        .empty {
            text-align: center;
            padding: 20px;
        }

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            select {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Manage Questions</h1>

    <div class="filter-box">
        <form method="GET" action="manage_questions.php">

            <label for="category">
                <strong>Filter by Category:</strong>
            </label>

            <select name="category" id="category">
                <option value="">All Categories</option>

                <?php foreach ($categories as $cat): ?>
                    <option
                        value="<?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>"
                        <?= $category === $cat ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>

            </select>

            <button type="submit">Show Questions</button>

            <a class="reset" href="manage_questions.php">
                Show All
            </a>

        </form>
    </div>

    <p class="count">
        Category:
        <?= htmlspecialchars($category ?: 'All Categories', ENT_QUOTES, 'UTF-8') ?>
        |
        Total Questions: <?= (int)$totalQuestions ?>
    </p>

    <div class="table-wrap">
        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Question</th>
                    <th>Correct Answer</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php if ($totalQuestions > 0): ?>

                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= (int)$row['id'] ?></td>

                            <td>
                                <?= htmlspecialchars(
                                    $row['category'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </td>

                            <td class="question">
                                <?= htmlspecialchars(
                                    $row['question'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $row['correct_answer'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </td>

                            <td>
                                <a
                                    class="edit"
                                    href="edit_question.php?id=<?= (int)$row['id'] ?>"
                                >Edit</a>

                                <a
                                    class="delete"
                                    href="delete_question.php?id=<?= (int)$row['id'] ?>"
                                    onclick="return confirm('Are you sure you want to delete this question?');"
                                >Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5" class="empty">
                            No questions found.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>
        </table>
    </div>

    <br>

    <a class="back" href="admin_dashboard.php">
        Back to Admin Dashboard
    </a>

</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
