<?php
require_once  '../api/init.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">

    <title>Registrering</title>
</head>
<body>
<h2>Registrering</h2>
<label>Velg bruker</label> <br>
<a href="resources/student.php" class="button-link">Student</a>
<a href="resources/professor.php" class="button-link">Foreleser</a>
<br>
<br>
<form action="src/login.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <input type="hidden" name="subject_id" value="anonymous"> <!-- Hidden field for anonymous login -->
    <button type="submit">Gjest innlogging</button>
</form>

<h2>Allerede bruker?</h2>
<form action="src/login.php" method="post">
    <label for="email">Email:</label><br>
    <input type="email" name="email" id="email" placeholder="example@example.com" size="30" autocomplete="email" required><br>
    <label for="password">Passord:</label><br>
    <input type="password" name="password" id="password" autocomplete="current-password" required><br>
    <a href="resources/forgotPassword.php">Glemt passord?</a>
    <input type="submit" name="login" value="Logg inn">
</form>

</body>
</html>
