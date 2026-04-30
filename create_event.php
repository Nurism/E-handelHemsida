<?php

require_once 'functions.php';

// Connect to database
$mysqli = connectToDb();

// Check if admin
if (!isAdmin($mysqli)) {
    redirectWithMessage('index.php', 'Du har inte behörighet att skapa konserter.');
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $artist = $_POST['artist'] ?? '';
    $venue = $_POST['venue'] ?? '';
    $event_date = $_POST['event_date'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $tickets_left = $_POST['tickets_left'] ?? 0;
    $image_url = $_POST['image_url'] ?? null;

    // Validate input
    if (empty($artist) || empty($venue) || empty($event_date) || $price <= 0 || $tickets_left <= 0) {
        $message = 'Alla obligatoriska fält måste fyllas i korrekt.';
    } else {
        createEvent($mysqli, $artist, $venue, $event_date, $description, $price, $tickets_left, $image_url);
        $message = 'Konserten har skapats!';
    }
}

?>


<!DOCTYPE html>
<html lang="sv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/main.css" rel="stylesheet">
    <title>Skapa konsert</title>
</head>

<body>

    <header>
        <h1>Skapa konsert</h1>
        <nav>
            <a href="index.php">Tillbaka</a>
            <a href="logout.php">Logga ut</a>
        </nav>
    </header>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <main>
        <form action="create_event.php" method="post">
            <label for="artist">Artist:*</label>
            <input type="text" id="artist" name="artist" required><br>

            <label for="venue">Plats:*</label>
            <input type="text" id="venue" name="venue" required><br>

            <label for="event_date">Datum:*</label>
            <input type="datetime-local" id="event_date" name="event_date" required><br>

            <label for="description">Beskrivning:</label>
            <textarea id="description" name="description"></textarea><br>

            <label for="price">Pris (SEK):*</label>
            <input type="number" id="price" name="price" step="0.01" min="0" required><br>

            <label for="tickets_left">Antal biljetter:*</label>
            <input type="number" id="tickets_left" name="tickets_left" min="1" required><br>

            <label for="image_url">Bild URL:</label>
            <input type="url" id="image_url" name="image_url"><br>

            <button type="submit">Skapa konsert</button>
        </form>
    </main>

</body>

</html>