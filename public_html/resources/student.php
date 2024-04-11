<?php
require_once  '../../api/init.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <title>Student Registration</title>
</head>
<body>
<form action="../src/register_student.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <label for="firstName">Fornavn:</label><br>
    <input type="text" name="firstName" id="firstName" required><br>

    <label for="lastName">Etternavn:</label><br>
    <input type="text" name="lastName" id="lastName" required><br>

    <label for="email">E-post:</label><br>
    <input type="email" id="email" name="email" autocomplete="email" size="30" required><br>

    <label for="fieldOfStudy">Studieretning:</label><br>
    <input type="text" name="fieldOfStudy" id="fieldOfStudy" required><br>

    <label for="classOf">Studiekull:</label><br>
    <input type="text" name="classOf" id="classOf" autocomplete="off" required><br>

    <label for="password">Passord:</label><br>
    <input type="password" name="password" id="password" autocomplete="new-password" required><br>

    <input type="submit" value="Registrer">
</form>
</body>
</html>