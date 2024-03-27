<?php
include 'db_connect.php';
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
            $insertTokenQuery = "INSERT INTO password_reset (email, token, expiry_time) VALUES (:email, :token , :expiry_time)";
            $stmt = $pdo->prepare($insertTokenQuery);
            $stmt->execute([':email' => $email, ':token' => $token, ':expiry_time' => $expiryTime]);

            // Send the reset email
            $resetLink = "http://158.39.188.207/steg1/public_html/src/passwordReset.php?token=$token";
            $subject = "Password Reset";
            $message = "Click the following link to reset your password: $resetLink";

            // Send the email
            mail($email, $subject, $message);

            echo "Password reset email sent, if email exists.";
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
}

