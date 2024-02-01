<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    updatePassword($_POST, $_SESSION['user']['id']);
}

function updatePassword($data, $userId)
{
    $pdo = Database::getInstance();
    $user = getUserById($userId, $pdo);

    if (!$user || !password_verify($data['old_password'], $user['password'])) {
        die("Incorrect old password");
    }

    if ($data['new_password'] !== $data['confirm_password']) {
        die("New passwords do not match");
    }

    $query = "UPDATE users SET password = :password WHERE id = :id";
    $stmt = $pdo->prepare($query);

    $newHashedPassword = password_hash($data['new_password'], PASSWORD_DEFAULT);
    $stmt->bindParam(':password', $newHashedPassword);
    $stmt->bindParam(':id', $userId);

    $stmt->execute();

    die("Password updated successfully");
}

function getUserById($userId, $pdo)
{
    $query = "SELECT * FROM users WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $userId);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

