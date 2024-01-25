<?php
session_start();

require "Functions.php";

$errors = array();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $errors = user_registration($_POST);

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
    <title>User Registration</title>
</head>
<body>
<h1>User Registration</h1>

<?php include('header.php')?>

<div>
    <?php if (!empty($errors)): ?>
        <?php foreach ($errors as $error): ?>
            <?= $error ?> <br>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<form method="post">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="text" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <input type="password" name="password2" placeholder="Retype Password" required><br>

    <br>
    <input type="submit" value="Register">
</form>
</div>
</body>
</html>

