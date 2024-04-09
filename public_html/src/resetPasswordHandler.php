<?php
include 'db_connect.php';
include_once  '../../api/logger.php';
require_once  '../../api/init.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    $email = $_POST['email'];
    $newPassword = $_POST['new_password'];

    updatePassword($email, $newPassword);

    $sanitizedEmail = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Display success message and redirect to index.php after 5 seconds
    writeToLog("Password reset successful for user: " . $sanitizedEmail);
    echo "Password updated successfully redirecting to login.";
    echo "<meta http-equiv='refresh' content='5;url=http://158.39.188.207/steg1/public_html/index.php'>";
}

function updatePassword($email, $newPassword)
{
    $pdo = db_connect::getInstance();

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $updateQuery = "UPDATE students SET password = :password WHERE email = :email";
    $stmt = $pdo->prepare($updateQuery);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':email', $email);

    $stmt->execute();

    // Remove the used token
    $deleteTokenQuery = "DELETE FROM password_reset WHERE email = :email";
    $stmt = $pdo->prepare($deleteTokenQuery);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
}

