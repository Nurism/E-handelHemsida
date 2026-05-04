<?php

$pageTitle = 'Registrera';
$extraNav = '<a href="index.php">Hem</a>';
include 'top.php';

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

    <main>
        <h2>Registrera</h2>
        <form action="register.php" method="post">
            <label>Namn: <input type="text" name="name" required></label><br>
            <label>E-post: <input type="email" name="email" required></label><br>
            <label>Lösenord: <input type="password" name="password" required></label><br>
            <button type="submit">Registrera</button>
        </form>
    </main>

<?php include 'bottom.php'; ?>