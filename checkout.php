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
if (empty($cart)) {
    redirectWithMessage('cart.php', 'Kundvagnen är tom.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = createOrder($db, $_SESSION['userId'], $cart);
    $_SESSION['cart'] = []; // Clear cart
    redirectWithMessage('index.php', 'Beställning genomförd! Ordernummer: ' . $orderId);
}

$total = 0;
foreach ($cart as $item) {
    $total += $item['event']['price'] * $item['quantity'];
}

?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/main.css" rel="stylesheet">
    <title>Kassa</title>
</head>
<body>
    <header>
        <h1>Konsertbiljetter</h1>
        <nav>
            <a href="index.php">Hem</a>
            <a href="cart.php">Kundvagn</a>
            <a href="logout.php">Logga ut</a>
        </nav>
    </header>

    <?php getMessage(); ?>

    <main>
        <h2>Kassa</h2>
        <div class="checkout">
            <?php foreach ($cart as $item): ?>
                <div class="checkout-item">
                    <h3><?= htmlspecialchars($item['event']['artist']) ?> - <?= htmlspecialchars($item['event']['venue']) ?></h3>
                    <p>Antal: <?= $item['quantity'] ?></p>
                    <p>Pris per biljett: <?= $item['event']['price'] ?> SEK</p>
                    <p>Delsumma: <?= $item['event']['price'] * $item['quantity'] ?> SEK</p>
                </div>
            <?php endforeach; ?>
            <p><strong>Totalt: <?= $total ?> SEK</strong></p>
            <form action="checkout.php" method="post">
                <button type="submit">Bekräfta köp</button>
            </form>
        </div>
    </main>
</body>
</html>