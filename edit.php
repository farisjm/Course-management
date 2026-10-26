<?php
include "db.php";

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("Invalid course ID");
}

$id = (int) $_GET['id'];
$message = "";

// UPDATE COURSE
if (isset($_POST['update'])) {
    $course = trim($_POST['course']);
    $credit = filter_var(
        $_POST['credit'],
        FILTER_VALIDATE_INT
    );

    if ($course !== "" && $credit !== false && $credit > 0) {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE Course SET course = ?, credit = ? WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt, "sii", $course, $credit, $id
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: index.php");
        exit;
    } else {
        $message = "Enter a valid course name and credit.";
    }
}

// GET SELECTED COURSE
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM Course WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$row) {
    die("Course not found");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Course</title>
</head>
<body>

<h2>Edit Course</h2>

<p style="color:red;">
    <?= htmlspecialchars($message) ?>
</p>

<form method="POST">
    <label>Course Name:</label><br>

    <input type="text" name="course"
        value="<?= htmlspecialchars($row['course']) ?>"
        required>
    <br><br>

    <label>Number of Credits:</label><br>

    <input type="number" name="credit"
        value="<?= $row['credit'] ?>"
        min="1" required>
    <br><br>

    <button type="submit" name="update">
        Update Course
    </button>
</form>

<br>

<a href="index.php">Back to Course List</a>

</body>
</html>