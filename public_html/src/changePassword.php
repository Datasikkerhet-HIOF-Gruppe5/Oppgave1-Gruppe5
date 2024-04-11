<?php

require_once  '../../api/init.php';

header("Content-Security-Policy: upgrade-insecure-requests");

include 'db_connect.php';
$pdo = Database::getInstance();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    updatePassword($_POST, $pdo);
}

function updatePassword($data, $pdo)
{
    $professorId = $_SESSION['user_id'];

    $professor = getProfessorById($professorId, $pdo);

    if (!$professor || !password_verify($data['old_password'], $professor['password'])) {
        die("Incorrect old password.");
    }

    if ($data['new_password'] !== $data['confirm_password']) {
        die("New passwords do not match.");
    }

    $password = $data['new_password'];
    if (strlen($password) < 10) {
        die("Password must be at least 10 characters long");
    }
    if (!preg_match('/[A-Z]/', $password)) {
        die("Password must contain at least one uppercase letter");
    }
    if (!preg_match('/[a-z]/', $password)) {
        die("Password must contain at least one lowercase letter");
    }
    if (!preg_match('/[0-9]/', $password)) {
        die("Password must contain at least one digit");
    }
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        die("Password must contain at least one special character");
    }

    $query = "UPDATE professors SET password = :password WHERE id = :id";

    $stmt = $pdo->prepare($query);

    $newHashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt->bindParam(':password', $newHashedPassword);
    $stmt->bindParam(':id', $professorId);
    $stmt->execute();

    die("Password updated successfully. <a href='../index.php'>Go back to login</a>");
}



function getProfessorById($userId, $pdo)
{
    $query = "SELECT * FROM professors WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $userId);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

