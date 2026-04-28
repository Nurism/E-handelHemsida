<?php

require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
require_once 'functions.php';

$db = connectToDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (getUserByEmail($db, $email)) {
        setMessage('E-postadressen är redan registrerad.');
    } else {
        createUser($db, $name, $email, $password);
        redirectWithMessage('login.php', 'Registrering lyckades! Logga in.');
    }
}

?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/main.css" rel="stylesheet">
    <title>Registrera</title>
</head>
<body>
    <header>
        <h1>Konsertbiljetter</h1>
        <nav>
            <a href="index.php">Hem</a>
            <a href="login.php">Logga in</a>
        </nav>
    </header>

    <?php getMessage(); ?>

    <main>
        <h2>Registrera</h2>
        <form action="register.php" method="post">
            <label>Namn: <input type="text" name="name" required></label><br>
            <label>E-post: <input type="email" name="email" required></label><br>
            <label>Lösenord: <input type="password" name="password" required></label><br>
            <button type="submit">Registrera</button>
        </form>
    </main>
</body>
</html>