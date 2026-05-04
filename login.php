<?php

$pageTitle = 'Logga in';
$extraNav = '<a href="index.php">Hem</a>';
include 'top.php';

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

    <main>
        <h2>Logga in</h2>
        <form action="login.php" method="post">
            <label>E-post: <input type="email" name="email" required></label><br>
            <label>Lösenord: <input type="password" name="password" required></label><br>
            <button type="submit">Logga in</button>
        </form>
    </main>

<?php include 'bottom.php'; ?>