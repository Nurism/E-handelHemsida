<?php

require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
require_once 'functions.php';

// Only logged-in users may add items to the cart.
if (!isLoggedIn()) {
    redirectWithMessage('login.php', 'Du måste logga in för att lägga till i kundvagnen.');
}

// Handle form submissions from event_detail.php.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = (int)($_POST['event_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);

    if ($eventId > 0 && $quantity > 0) {
        addToCart($eventId, $quantity);
        redirectWithMessage('cart.php', 'Tillagt i kundvagnen.');
    } else {
        redirectWithMessage('index.php', 'Felaktig förfrågan.');
    }
} else {
    header("Location: index.php");
    exit();
}

?>