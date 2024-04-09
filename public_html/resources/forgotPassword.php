<?php
require_once  '../../api/init.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <title>Glemt passord</title>
</head>
<body>
<form action="../src/forgotPassword.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <label for="email">Email:</label><br>
    <input type="email" name="email" id="email" placeholder="example@example.com" size="30" required><br>
    <button type="submit">Send</button>
</form>
</body>
</html>
