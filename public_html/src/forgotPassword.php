<?php
include 'db_connect.php';
require_once  '../../api/init.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    try {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Generate a unique token regardless of whether the email exists or not
        $token = bin2hex(random_bytes(32));

        // If the email exists, store the token and expiry time in the database
        if ($user) {
            $expiryTime = time() + (60 * 60); // Token expires in 1 hour
            $insertTokenQuery = "INSERT INTO password_reset (email, token, expiry_time) VALUES (:email, :token , :expiry_time)";
            $stmt = $pdo->prepare($insertTokenQuery);
            $stmt->execute([':email' => $email, ':token' => $token, ':expiry_time' => $expiryTime]);
        }

        // Send the reset email regardless of whether the email exists or not
        $resetLink = "http://158.39.188.207/steg2/public_html/src/passwordReset.php?token=$token";
        $subject = "Password Reset";
        $message = "Click the following link to reset your password: $resetLink";

        // If the email does not exist, do not actually send the email
        if (!$user) {
            // Log the fact that an email would have been sent here
            // You can log this information to keep track of attempted password resets
            // but ensure that you don't log the email address itself for privacy reasons.
        } else {
            // Send the email
            mail($email, $subject, $message);
        }

        // Provide a generic confirmation message
        echo "Password reset email sent, if the provided email is registered.";

    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
}
?>
