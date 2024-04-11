<?php
require_once  '../../api/init.php';
header("Content-Security-Policy: upgrade-insecure-requests");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foreleser</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<form action="../src/changePassword.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <label for="old_password">Gammelt passord:</label><br>
    <input type="password" id="old_password" name="old_password" required><br>

    <label for="new_password">Nytt passord:</label><br>
    <input type="password" id="new_password" name="new_password" required><br>

    <label for="confirm_password">Bekreft Passord:</label><br>
    <input type="password" id="confirm_password" name="confirm_password" required><br>

    <input type="submit" value="Bytt passord"><br>
</form>

</body>
</html>
