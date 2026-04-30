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
        <h2>Tillgängliga konserter</h2>
        <div class="events">
            <?php foreach ($events as $event): ?>
                <a href="event_detail.php?id=<?= $event['id'] ?>" class="event-link">
                    <div class="event">
                        <?php if ($event['image_url']): ?>
                            <img src="<?= htmlspecialchars($event['image_url']) ?>" alt="Bild för <?= htmlspecialchars($event['artist']) ?>" class="event-image">
                        <?php else: ?>
                            <div class="no-image">Ingen bild</div>
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($event['artist']) ?></h3>
                        <p class="event-date"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($event['event_date']))) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </main>

</body>

</html>