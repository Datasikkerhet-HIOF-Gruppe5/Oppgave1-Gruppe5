<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $newPassword = $_POST['new_password'];

    updatePassword($email, $newPassword);

    // Display success message and redirect to index.html after 5 seconds
    echo "Password updated successfully redirecting to login.";
    echo "<meta http-equiv='refresh' content='5;url=/index.html'>";
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

