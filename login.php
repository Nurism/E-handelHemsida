<?php

require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
require_once 'functions.php';

$db = connectToDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (login($db, $email, $password)) {
        redirectWithMessage('index.php', 'Inloggning lyckades!');
    } else {
        setMessage('Fel e-post eller lösenord.');
    }
}

?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/main.css" rel="stylesheet">
    <title>Logga in</title>
</head>
<body>
    <header>
        <h1>Konsertbiljetter</h1>
        <nav>
            <a href="index.php">Hem</a>
            <a href="register.php">Registrera</a>
        </nav>
    </header>

    <?php getMessage(); ?>

    <main>
        <h2>Logga in</h2>
        <form action="login.php" method="post">
            <label>E-post: <input type="email" name="email" required></label><br>
            <label>Lösenord: <input type="password" name="password" required></label><br>
            <button type="submit">Logga in</button>
        </form>
    </main>
</body>
</html>