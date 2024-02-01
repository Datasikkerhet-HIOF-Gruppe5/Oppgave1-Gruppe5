<?php

// Include necessary files and start session
include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    try {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user === false) {
            echo "User not found.";
        } else {
            // Generate a unique token
            $token = bin2hex(random_bytes(32));

            // Store the token and expiry time in the database
            $expiryTime = time() + (60 * 60); // Token expires in 1 hour
            $insertTokenQuery = "INSERT INTO password_reset (email, token, expiry_time) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($insertTokenQuery);
            $stmt->execute([$email, $token, $expiryTime]);

            // Send the reset email
            $resetLink = "http://localhost/resetPassword.php?token=$token";
            $subject = "Password Reset";
            $message = "Click the following link to reset your password: $resetLink";

            // Send the email
            mail($email, $subject, $message);

            echo "Password reset email sent.";
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>
<body>

<!-- Display the form to initiate the password reset -->
<form method="post" action="forgotPassword.php">
    <label for="email">Enter your email:</label>
    <input type="email" name="email" required>
    <input type="submit" value="Reset Password">
</form>

</body>
</html>
