<?php
session_start();

require "Functions.php";

$errors = array();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $errors = lecturer_registration($_POST);

    if (empty($errors)) {
        header("Location: login.php");
        die;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Lecturer Register</title>
</head>
<body>
<h1>Lecturer Register</h1>

<?php include('header.php')?>

<div>
    <?php if (!empty($errors)): ?>
        <?php foreach ($errors as $error): ?>
            <?= $error ?> <br>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<form method="post" enctype="multipart/form-data">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="text" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <input type="password" name="password2" placeholder="Retype Password" required><br>
    <input type="file" name="image" accept="image/*" required><br>
    <input type="text" name="course" placeholder="Course Name" required><br>
    <input type="text" name="pin" placeholder="PIN Code" required><br>
    <input type="submit" value="Register">
</form>
</div>
</body>
</html>