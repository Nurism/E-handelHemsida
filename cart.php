<?php

$pageTitle = 'Kundvagn';
$extraNav = '<a href="index.php">Hem</a>';
include 'top.php';

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

<?php include 'bottom.php'; ?>