<?php
include "db.php";

$message = "";

// INSERT COURSE
if (isset($_POST['save'])) {
    $course = trim($_POST['course']);
    $credit = filter_var(
        $_POST['credit'],
        FILTER_VALIDATE_INT
    );

    if ($course !== "" && $credit !== false && $credit > 0) {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO Course (course, credit) VALUES (?, ?)"
        );

        mysqli_stmt_bind_param($stmt, "si", $course, $credit);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: index.php");
        exit;
    } else {
        $message = "Enter a valid course name and credit.";
    }
}

// DISPLAY COURSES
$result = mysqli_query($conn, "SELECT * FROM Course ORDER BY id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Course Management System</title>

    <style>
        
:root {
    --green-dark: #164e37;
    --green: #16804a;
    --green-light: #e7f5eb;
    --background: #f3f7f4;
    --text: #20352a;
    --muted: #718078;
    --border: #e0e9e2;
    --white: #ffffff;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 40px 24px;
    font-family: "Segoe UI", Arial, sans-serif;
    background: var(--background);
    color: var(--text);
    line-height: 1.6;
}

.container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 32px;
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 20px;
    box-shadow: 0 10px 35px rgba(22, 78, 55, 0.07);
}

h2 {
    margin: 0 0 28px;
    padding: 22px 24px;
    background: var(--green-dark);
    color: var(--white);
    border-radius: 14px;
    font-size: 28px;
    letter-spacing: -0.5px;
}

h3 {
    margin: 28px 0 16px;
    color: var(--green-dark);
    font-size: 20px;
}

form {
    margin-bottom: 20px;
}

label {
    display: block;
    margin: 16px 0 8px;
    font-weight: 600;
}

input[type="text"],
input[type="number"] {
    display: block;
    width: 100%;
    padding: 14px 16px;
    margin: 0 0 12px;
    border: 1px solid #d5e1d8;
    border-radius: 10px;
    background: #fbfdfb;
    font: inherit;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
}

input:focus {
    border-color: var(--green);
    box-shadow: 0 0 0 4px rgba(22, 128, 74, 0.10);
}

button {
    display: inline-block;
    padding: 12px 20px;
    margin: 6px 0;
    border: none;
    border-radius: 9px;
    background: var(--green);
    color: white;
    font: inherit;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s, background 0.2s;
}

button:hover {
    background: var(--green-dark);
    transform: translateY(-2px);
}

table {
    width: 100%;
    margin-top: 20px;
    border-collapse: separate;
    border-spacing: 0;
    overflow: hidden;
    border: 1px solid var(--border);
    border-radius: 12px;
}

th {
    padding: 16px;
    background: var(--green-dark);
    color: white;
    text-align: left;
    font-size: 14px;
}

td {
    padding: 16px;
    border-bottom: 1px solid var(--border);
    vertical-align: middle;
}

tr:last-child td {
    border-bottom: none;
}

tbody tr:hover,
table tr:hover td {
    background: #f5faf6;
}

a {
    color: var(--green);
    font-weight: 600;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

button.delete {
    background: #dc4545;
}

button.delete:hover {
    background: #b42323;
}

@media (max-width: 650px) {
    body {
        padding: 14px;
    }

    .container {
        padding: 16px;
        border-radius: 14px;
    }

    h2 {
        padding: 18px;
        font-size: 22px;
    }

    table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }

    th, td {
        padding: 12px;
    }
}

    </style>
</head>

<body>
<div class="container">

    <h2>Course Management System</h2>

    <h3>Add New Course</h3>

    <p style="color:red;">
        <?= htmlspecialchars($message) ?>
    </p>

    <form method="POST" action="index.php">
        <label>Course Name:</label><br>
        <input type="text" name="course"
               placeholder="Enter course name" required>
        <br>

        <label>Number of Credits:</label><br>
        <input type="number" name="credit"
               min="1" placeholder="Enter credits" required>
        <br>

        <button type="submit" name="save">
            Save Course
        </button>
    </form>

    <h3>Course List</h3>

    <table>
        <tr>
            <th>ID</th>
            <th>Course Name</th>
            <th>Credits</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= $row['id'] ?></td>

            <td>
                <?= htmlspecialchars($row['course']) ?>
            </td>

            <td><?= $row['credit'] ?></td>

            <td>
                <a href="edit.php?id=<?= $row['id'] ?>">
                    Edit
                </a>

                |

                <form method="POST" action="delete.php"
                      style="display:inline;"
                      onsubmit="return confirm('Delete this course?');">
                    <input type="hidden" name="id"
                           value="<?= $row['id'] ?>">
                    <button type="submit" class="delete">
                        Delete
                    </button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>

</div>
</body>
</html>