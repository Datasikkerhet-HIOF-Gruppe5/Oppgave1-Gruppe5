<?php

include 'db_connect.php';
require_once  '../../api/init.php';

// Function to get the number of failed attempts from session
function getFailedAttempts() {
    return isset($_SESSION['failed_attempts']) ? $_SESSION['failed_attempts'] : 0;
}

// Function to increment failed attempts
function incrementFailedAttempts() {
    $_SESSION['failed_attempts'] = getFailedAttempts() + 1;
}

// Function to reset failed attempts
function resetFailedAttempts() {
    $_SESSION['failed_attempts'] = 0;
}

// Function to check if cooldown is active
function isCooldownActive(): bool
{
    return isset($_SESSION['cooldown_end_time']) && $_SESSION['cooldown_end_time'] > time();
}

// Function to activate cooldown
function activateCooldown() {
    $cooldown_duration = 60 * (2 ** getFailedAttempts()); // Doubling cooldown duration
    $_SESSION['cooldown_end_time'] = time() + $cooldown_duration;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    // Check if cooldown is active
    if (isCooldownActive()) {
        $remaining_cooldown = $_SESSION['cooldown_end_time'] - time();
        echo "You have reached maximum failed attempts. Please try again after cooldown period. Cooldown remaining: $remaining_cooldown seconds";
        exit;
    }

    // Handle anonymous login
    if (!empty($_POST['subject_id'])) {
        $_SESSION['user_id'] = $_POST['subject_id'];
        $_SESSION['user_role'] = 'anonymous';
        header("Location: ../src/anonReadMsg.php");
        exit;
    }

    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (!$email) {
        echo "Invalid email format.";
        exit;
    }

    try {
        // Check if user is a student
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = 'student';
            echo "Student login successful.";
            // Reset failed attempts on successful login
            resetFailedAttempts();
            // Redirect to a student-specific page or dashboard if needed
            header("Location: ../src/studReadMsg.php");
            exit;
        }
    } catch (PDOException $e) {
        // Handle database errors
        echo "Database error: " . $e->getMessage();
    }

    try {
        // Check if user is a professor
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM professors WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = 'professor';
            echo "Professor login successful.";
            // Reset failed attempts on successful login
            resetFailedAttempts();
            // Redirect to a professor-specific page or dashboard if needed
            header("Location: ../src/profReadMsg.php");
            exit;
        }
    } catch (PDOException $e) {
        // Handle database errors
        echo "Database error: " . $e->getMessage();
    }

    // If the code reaches this point, it means login failed
    incrementFailedAttempts();
    echo "Invalid credentials.";

    // Activate cooldown after maximum failed attempts
    if (getFailedAttempts() >= 3) {
        activateCooldown();
    }

}

