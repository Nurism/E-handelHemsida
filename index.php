<?php

require_once 'functions.php';

// Connect to database
$mysqli = connectToDb();

// Get all events
$events = getEvents($mysqli);

?>


<!DOCTYPE html>
<html lang="sv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/main.css" rel="stylesheet">
    <title>Konsertbiljetter</title>
</head>

<body>

    <header>
        <h1>Konsertbiljetter</h1>
        <nav>
            <?php if (isLoggedIn()): ?>
                <span>Välkommen, <?= htmlspecialchars(getUserById($mysqli, $_SESSION['userId'])['name']) ?>!</span>
                <a href="cart.php">Kundvagn</a>
                <a href="logout.php">Logga ut</a>
            <?php else: ?>
                <a href="login.php">Logga in</a>
                <a href="register.php">Registrera</a>
            <?php endif; ?>
        </nav>
    </header>

    <?php getMessage(); ?>

    <main>
        <h2>Tillgängliga konserter</h2>
        <div class="events">
            <?php foreach ($events as $event): ?>
                <div class="event">
                    <h3><?= htmlspecialchars($event['artist']) ?></h3>
                    <p>Plats: <?= htmlspecialchars($event['venue']) ?></p>
                    <p>Datum: <?= htmlspecialchars($event['event_date']) ?></p>
                    <p>Pris: <?= htmlspecialchars($event['price']) ?> SEK</p>
                    <p>Biljetter kvar: <?= htmlspecialchars($event['tickets_left']) ?></p>
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
            <?php endforeach; ?>
        </div>
    </main>

</body>

</html>