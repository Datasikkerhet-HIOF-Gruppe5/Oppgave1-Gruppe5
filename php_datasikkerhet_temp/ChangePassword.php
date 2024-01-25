<?php
session_start();

require "Functions.php";

$errors = array();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $errors = updatePassword($_POST);

    if (empty($errors)) {
        header("Location: profile.php");
        die;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Change Password</title>
</head>
<body>
<h1>Change Password</h1>

<?php include('header.php')?>

<div>
    <?php if (!empty($errors)): ?>
        <?php foreach ($errors as $error): ?>
            <?= $error ?> <br>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<form method="post">
    <input type="password" name="old_password" placeholder="Old Password" required><br>
    <input type="password" name="new_password" placeholder="New Password" required><br>
    <input type="password" name="confirm_password" placeholder="Confirm New Password" required><br>

    <br>
    <input type="submit" value="Change Password">
</form>
</body>
</html>