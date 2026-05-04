<?php

$pageTitle = 'VenueNow';
$extraNav = '<a href="index.php">Tillbaka till konserter</a>';
include 'top.php';

$eventId = $_GET['id'] ?? null;
if (!$eventId) {
    redirectWithMessage('index.php', 'Ogiltigt event ID.');
}

$event = getEventById($db, $eventId);
if (!$event) {
    redirectWithMessage('index.php', 'Eventet hittades inte.');
}

$dynamicTitle = htmlspecialchars($event['artist']) . ' - VenueNow';
echo '<script>document.title = ' . json_encode($dynamicTitle) . ';</script>';

?>

    <main>
        <div class="event-detail">
            <?php if ($event['image_url']): ?>
                <img src="<?= htmlspecialchars($event['image_url']) ?>" alt="Bild för <?= htmlspecialchars($event['artist']) ?>" class="event-image">
            <?php endif; ?>
            <h2><?= htmlspecialchars($event['artist']) ?></h2>
            <?php if ($event['description']): ?>
                <p class="description"><?= htmlspecialchars($event['description']) ?></p>
            <?php endif; ?>
            <p><strong>Plats:</strong> <?= htmlspecialchars($event['venue']) ?></p>
            <p><strong>Datum:</strong> <?= htmlspecialchars($event['event_date']) ?></p>
            <p><strong>Pris:</strong> <?= htmlspecialchars($event['price']) ?> SEK</p>
            <p><strong>Biljetter kvar:</strong> <?= htmlspecialchars($event['tickets_left']) ?></p>

            <?php if ($event['tickets_left'] > 0 && isLoggedIn()): ?>
                <form action="add_to_cart.php" method="post">
                    <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                    <label>Antal: <input type="number" name="quantity" min="1" max="<?= $event['tickets_left'] ?>" value="1"></label>
                    <button type="submit">Lägg i kundvagn</button>
                </form>
            <?php elseif (!isLoggedIn()): ?>
                <p><a href="login.php">Logga in för att köpa</a></p>
            <?php else: ?>
                <p>Slut på biljetter</p>
            <?php endif; ?>
        </div>
    </main>

<?php include 'bottom.php'; ?>