<?php
include "db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

if (!isset($_POST['id']) ||
    !ctype_digit($_POST['id'])) {
    die("Invalid course ID");
}

$id = (int) $_POST['id'];

$stmt = mysqli_prepare(
    $conn,
    "DELETE FROM Course WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: index.php");
exit;
?>