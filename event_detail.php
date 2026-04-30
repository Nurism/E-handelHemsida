<?php

require_once 'functions.php';

// Connect to database
$mysqli = connectToDb();

$eventId = $_GET['id'] ?? null;
if (!$eventId) {
    redirectWithMessage('index.php', 'Ogiltigt event ID.');
}

$event = getEventById($mysqli, $eventId);
if (!$event) {
    redirectWithMessage('index.php', 'Eventet hittades inte.');
}

?>


<!DOCTYPE html>
<html lang="sv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/main.css" rel="stylesheet">
    <title><?= htmlspecialchars($event['artist']) ?> - Konsertbiljetter</title>
</head>

<body>

    <header>
        <h1>Konsertbiljetter</h1>
        <nav>
            <a href="index.php">Tillbaka till konserter</a>
            <?php if (isLoggedIn()): ?>
                <span>Välkommen, <?= htmlspecialchars(getUserById($mysqli, $_SESSION['userId'])['name']) ?>!</span>
                <a href="cart.php">Kundvagn</a>
                <?php if (isAdmin($mysqli)): ?>
                    <a href="create_event.php">Skapa konsert</a>
                <?php endif; ?>
                <a href="logout.php">Logga ut</a>
            <?php else: ?>
                <a href="login.php">Logga in</a>
                <a href="register.php">Registrera</a>
            <?php endif; ?>
        </nav>
    </header>

    <?php getMessage(); ?>

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

</body>

</html>