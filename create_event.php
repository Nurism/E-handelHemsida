<?php

$pageTitle = 'Skapa konsert';
$extraNav = '<a href="index.php">Tillbaka</a>';
include 'top.php';

// Check if admin
if (!isAdmin($db)) {
    redirectWithMessage('index.php', 'Du har inte behörighet att skapa konserter.');
}

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
        setMessage('Alla obligatoriska fält måste fyllas i korrekt.');
    } else {
        createEvent($db, $artist, $venue, $event_date, $description, $price, $tickets_left, $image_url);
        setMessage('Konserten har skapats!');
    }
}

?>

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

<?php include 'bottom.php'; ?>