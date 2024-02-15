<?php
session_start();

include 'db_connect.php';
$pdo = Database::getInstance();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    $query = "UPDATE professors SET password = :password WHERE id = :id";

    $stmt = $pdo->prepare($query);

    $newHashedPassword = password_hash($data['new_password'], PASSWORD_DEFAULT);

    $stmt->bindParam(':password', $newHashedPassword);
    $stmt->bindParam(':id', $professorId);
    $stmt->execute();

    die("Password updated successfully. <a href='../index.html'>Go back to login</a>");
}


function getProfessorById($userId, $pdo)
{
    $query = "SELECT * FROM professors WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $userId);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

