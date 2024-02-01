<?php

include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $token = $_GET['token'];

    // Validate the token
    $pdo = Database::getInstance();
    $currentTime = time();

    $tokenQuery = "SELECT * FROM password_reset WHERE token = ? AND expiry_time > ?";
    $stmt = $pdo->prepare($tokenQuery);
    $stmt->execute([$token, $currentTime]);
    $tokenData = $stmt->fetch();

    if ($tokenData === false) {
        die("Invalid or expired token.");
    }

    // Display the password reset form
    echo "<form method='post' action='resetPasswordHandler.php'>";
    echo "<input type='hidden' name='email' value='{$tokenData['email']}'>";
    echo "<label for='new_password'>New Password:</label>";
    echo "<input type='password' name='new_password' required><br>";
    echo "<label for='confirm_password'>Confirm Password:</label>";
    echo "<input type='password' name='confirm_password' required><br>";
    echo "<input type='submit' value='Reset Password'>";
    echo "</form>";
}

?>
