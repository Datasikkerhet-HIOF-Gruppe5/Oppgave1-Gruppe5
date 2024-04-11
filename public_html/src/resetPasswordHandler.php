<?php
include 'db_connect.php';
include_once  '../../api/logger.php';
require_once  '../../api/init.php';

header("Content-Security-Policy: upgrade-insecure-requests");

$errors = array();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = "CSRF token validation failed.";
    }

    $email = $_POST['email'];
    $newPassword = $_POST['new_password'];

    // Password rules
    if (strlen($newPassword) < 10) {
        $errors[] = "Password must be at least 10 characters long";
    }
    if (!preg_match('/[A-Z]/', $newPassword)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }
    if (!preg_match('/[a-z]/', $newPassword)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }
    if (!preg_match('/[0-9]/', $newPassword)) {
        $errors[] = "Password must contain at least one digit";
    }
    if (!preg_match('/[^A-Za-z0-9]/', $newPassword)) {
        $errors[] = "Password must contain at least one special character";
    }

    if (empty($errors)) {
        updatePassword($email, $newPassword);

        $sanitizedEmail = filter_var($email, FILTER_SANITIZE_EMAIL);

        // Display success message and redirect to index.php after 5 seconds
        writeToLog("Password reset successful for user: " . $sanitizedEmail);
        echo "Password updated successfully. Redirecting to login.";
        echo "<meta http-equiv='refresh' content='5;url=https://158.39.188.207/steg2/public_html/'/>";
    } else {
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
    }
}

function updatePassword($email, $newPassword)
{
    $pdo = Database::getInstance();

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
?>
