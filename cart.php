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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove'])) {
        $eventId = (int)($_POST['event_id'] ?? 0);
        removeFromCart($eventId);
        header("Location: cart.php");
        exit();
    }

    if (isset($_POST['update'])) {
        $eventId = (int)($_POST['event_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);

        if ($eventId <= 0 || $quantity < 1) {
            redirectWithMessage('cart.php', 'Antalet måste vara minst 1.');
        }

        $event = getEventById($db, $eventId);
        if (! $event) {
            redirectWithMessage('cart.php', 'Ogiltigt event.');
        }

        if ($quantity > $event['tickets_left']) {
            redirectWithMessage('cart.php', 'Det finns inte så många biljetter kvar.');
        }

        if (updateCartQuantity($db, $eventId, $quantity)) {
            redirectWithMessage('cart.php', 'Antalet uppdaterat.');
        }

        redirectWithMessage('cart.php', 'Kunde inte uppdatera antal.');
    }
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
                        <div class="cart-item-media">
                            <?php if (!empty($item['event']['image_url'])): ?>
                                <img src="<?= htmlspecialchars($item['event']['image_url']) ?>" alt="Bild för <?= htmlspecialchars($item['event']['artist']) ?>" class="cart-item-image">
                            <?php else: ?>
                                <div class="cart-item-placeholder">Bild</div>
                            <?php endif; ?>
                        </div>
                        <div class="cart-item-info">
                            <h3><?= htmlspecialchars($item['event']['artist']) ?></h3>
                            <p><?= htmlspecialchars($item['event']['venue']) ?></p>
                            <div class="cart-item-controls">
                                <form action="cart.php" method="post" class="cart-item-actions">
                                    <input type="hidden" name="event_id" value="<?= $item['event']['id'] ?>">
                                    <label class="cart-item-quantity">
                                        Antal:
                                        <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="<?= max(1, (int)$item['event']['tickets_left']) ?>">
                                    </label>
                                    <button type="submit" name="update">Uppdatera</button>
                                </form>

                                <form action="cart.php" method="post" class="cart-item-actions cart-item-remove-form">
                                    <input type="hidden" name="event_id" value="<?= $item['event']['id'] ?>">
                                    <button type="submit" name="remove">Ta bort</button>
                                </form>
                            </div>
                            <p class="cart-item-price">Pris: <?= $item['event']['price'] * $item['quantity'] ?> SEK</p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <p>Totalt: <?= $total ?> SEK</p>
                <a href="checkout.php">Gå till kassan</a>
            </div>
        <?php endif; ?>
    </main>

<?php include 'bottom.php'; ?>