<?php

require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
require_once 'functions.php';

$db = connectToDb();

if (!isLoggedIn()) {
    redirectWithMessage('login.php', 'Du måste logga in.');
}

$cart = getCart($db);
$total = 0;
foreach ($cart as $item) {
    $total += $item['event']['price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove'])) {
    $eventId = (int)($_POST['event_id'] ?? 0);
    removeFromCart($eventId);
    header("Location: cart.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/main.css" rel="stylesheet">
    <title>Kundvagn</title>
</head>
<body>
    <header>
        <h1>Konsertbiljetter</h1>
        <nav>
            <a href="index.php">Hem</a>
            <a href="logout.php">Logga ut</a>
        </nav>
    </header>

    <?php getMessage(); ?>

    <main>
        <h2>Din kundvagn</h2>
        <?php if (empty($cart)): ?>
            <p>Kundvagnen är tom.</p>
        <?php else: ?>
            <div class="cart">
                <?php foreach ($cart as $item): ?>
                    <div class="cart-item">
                        <h3><?= htmlspecialchars($item['event']['artist']) ?></h3>
                        <p>Antal: <?= $item['quantity'] ?></p>
                        <p>Pris: <?= $item['event']['price'] * $item['quantity'] ?> SEK</p>
                        <form action="cart.php" method="post">
                            <input type="hidden" name="event_id" value="<?= $item['event']['id'] ?>">
                            <button type="submit" name="remove">Ta bort</button>
                        </form>
                    </div>
                <?php endforeach; ?>
                <p>Totalt: <?= $total ?> SEK</p>
                <a href="checkout.php">Gå till kassan</a>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>