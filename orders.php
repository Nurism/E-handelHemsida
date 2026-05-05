<?php

$pageTitle = 'Mina ordrar';
$extraNav = '<a href="index.php">Hem</a>';
include 'top.php';

if (!isLoggedIn()) {
    redirectWithMessage('login.php', 'Du måste logga in för att se dina ordrar.');
}

$orders = getOrdersByUser($db, $_SESSION['userId']);
?>

    <main>
        <h2>Mina ordrar</h2>

        <?php if (empty($orders)): ?>
            <p>Du har inga tidigare beställningar.</p>
        <?php else: ?>
            <div class="orders">
                <?php foreach ($orders as $order): ?>
                    <?php $items = getOrderItems($db, $order['id']); ?>
                    <div class="order-card">
                        <div class="order-card-header">
                            <div>
                                <h3>Order #<?= htmlspecialchars($order['id']) ?></h3>
                                <p>Totalt: <?= htmlspecialchars(number_format($order['total'], 0, ',', ' ')) ?> SEK</p>
                            </div>
                        </div>
                        <div class="order-items">
                            <?php foreach ($items as $item): ?>
                                <div class="order-item">
                                    <div class="order-item-details">
                                        <strong><?= htmlspecialchars($item['artist'] ?? 'Event') ?></strong>
                                        <p><?= htmlspecialchars($item['venue'] ?? '') ?></p>
                                        <p>Antal: <?= htmlspecialchars($item['quantity']) ?></p>
                                    </div>
                                    <div class="order-item-price">
                                        <p><?= htmlspecialchars(number_format($item['price_each'], 0, ',', ' ')) ?> SEK/st</p>
                                        <p><strong><?= htmlspecialchars(number_format($item['price_each'] * $item['quantity'], 0, ',', ' ')) ?> SEK</strong></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

<?php include 'bottom.php'; ?>
