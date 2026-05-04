<?php

$pageTitle = 'Kassa';
$extraNav = '<a href="index.php">Hem</a> <a href="cart.php">Kundvagn</a>';
include 'top.php';

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

<?php include 'bottom.php'; ?>