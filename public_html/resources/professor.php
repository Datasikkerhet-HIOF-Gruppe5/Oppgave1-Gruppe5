<?php
require_once  '../../api/init.php';
header("Content-Security-Policy: upgrade-insecure-requests");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <title>Foreleser</title>
</head>
<body>
<form action="../src/register_professor.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <label for="firstName">Fornavn:</label><br>
    <input type="text" name="firstName" id="firstName" required><br>

    <label for="lastName">Etternavn:</label><br>
    <input type="text" name="lastName" id="lastName" required><br>

    <label for="email">E-post</label><br>
    <input type="email" id="email" name="email" size="30" required><br>

    <label>Legg til bilde:</label><br>
    <input type="file" name="picture"><br>

    <label>Emne:</label><br>
    <label for="subjectName"></label><input type="text" name="subjectName" id="subjectName" required><br>

    <label for="subjectPIN">EmnePIN:</label><br>
    <input type="text" name="subjectPIN" id="subjectPIN" maxlength="4" required/><br>

    <label>Passord:</label><br>
    <label>
        <input type="password" name="password" required>
    </label><br>
    <input type="submit" value="Register"><br>
</form>
<style>
    body {
        font-family: 'Courier New', Courier, monospace;
        background-color: #f0f0f0;
        margin: 0;
        padding: 20px;
        color: #333;
    }
    label {
        display: block;
        margin-top: 20px;
    }
    input[type="text"], input[type="email"], input[type="password"], input[type="file"] {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }
    input[type="submit"] {
        background-color: #000080; /* Navy blue */
        color: white;
        border: none;
        padding: 10px 20px;
        margin-top: 10px;
        border-radius: 5px;
        cursor: pointer;
    }
</style>
</body>
</html>