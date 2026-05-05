<?php

session_start();
ob_start();

// Load installed packages
require_once 'vendor/autoload.php';

// Load secrets from the file .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


function connectToDb() {
    $dbHost = 'ostrawebb.se';
    $dbUser = $_ENV['DB_USER'];
    $dbPassword = $_ENV['DB_PASS'];
    $dbDatabase = $_ENV['DB_USER'];

    $db = new mysqli($dbHost, $dbUser, $dbPassword, $dbDatabase);

    if ($db->connect_error) {
        die('Database connection failed: ' . $db->connect_error);
    }

    return $db;
}

function setMessage($message) {
    $_SESSION['message'] = $message;
}

function getMessage() {
    if (isset($_SESSION['message'])){
        echo '<div id="status-popup" class="popup-message">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']);
    }
}

function createUser($db, $name, $email, $password) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $statement = $db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $statement->bind_param('sss', $name, $email, $hashedPassword);
    $statement->execute();
}

function getUserByEmail($db, $email){
    $statement = $db->prepare("SELECT * FROM users WHERE email = ?");
    $statement->bind_param('s', $email);
    $statement->execute();
    $result = $statement->get_result();
    return $result->fetch_assoc();
}

function getUserById($db, $userId){
    $statement = $db->prepare("SELECT * FROM users WHERE id = ?");
    $statement->bind_param('i', $userId);
    $statement->execute();
    $result = $statement->get_result();
    return $result->fetch_assoc();
}

function redirectWithMessage($url, $message){
    $_SESSION['message'] = $message;
    header("Location: " . $url);
    exit();
}

function login($db, $email, $password){
    $user = getUserByEmail($db, $email);

    if (! $user) {
        return false;
    }

    if (! password_verify($password, $user['password'])) {
        return false;
    }

    $_SESSION['loggedIn'] = true;
    $_SESSION['userId'] = $user['id'];
    return true;
}

function isLoggedIn(){
    if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != TRUE) {
        return FALSE;
    } 

    return TRUE;
}

function getEvents($db) {
    $result = $db->query("SELECT * FROM events ORDER BY event_date");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getEventById($db, $id) {
    $statement = $db->prepare("SELECT * FROM events WHERE id = ?");
    $statement->bind_param('i', $id);
    $statement->execute();
    $result = $statement->get_result();
    return $result->fetch_assoc();
}

function addToCart($eventId, $quantity) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (isset($_SESSION['cart'][$eventId])) {
        $_SESSION['cart'][$eventId] += $quantity;
    } else {
        $_SESSION['cart'][$eventId] = $quantity;
    }
}

function getCart($db) {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return [];
    }
    $cart = [];
    foreach ($_SESSION['cart'] as $eventId => $quantity) {
        $event = getEventById($db, $eventId);
        if ($event) {
            $cart[] = ['event' => $event, 'quantity' => $quantity];
        }
    }
    return $cart;
}

function removeFromCart($eventId) {
    if (isset($_SESSION['cart'][$eventId])) {
        unset($_SESSION['cart'][$eventId]);
    }
}

function updateCartQuantity($db, $eventId, $quantity) {
    if ($quantity < 1) {
        return false;
    }

    $event = getEventById($db, $eventId);
    if (! $event) {
        return false;
    }

    if ($quantity > $event['tickets_left']) {
        return false;
    }

    $_SESSION['cart'][$eventId] = $quantity;
    return true;
}

function createOrder($db, $userId, $cart) {
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['event']['price'] * $item['quantity'];
    }
    $statement = $db->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
    $statement->bind_param('id', $userId, $total);
    $statement->execute();
    $orderId = $db->insert_id;

    foreach ($cart as $item) {
        $statement = $db->prepare("INSERT INTO order_items (order_id, event_id, quantity, price_each) VALUES (?, ?, ?, ?)");
        $statement->bind_param('iiid', $orderId, $item['event']['id'], $item['quantity'], $item['event']['price']);
        $statement->execute();
        // Update tickets_left
        $newTickets = $item['event']['tickets_left'] - $item['quantity'];
        $stmt = $db->prepare("UPDATE events SET tickets_left = ? WHERE id = ?");
        $stmt->bind_param('ii', $newTickets, $item['event']['id']);
        $stmt->execute();
    }
    return $orderId;
}

function logout() {
    session_destroy();
    header("Location: index.php");
    exit();
}

function isAdmin($db) {
    if (!isLoggedIn()) {
        return false;
    }
    $user = getUserById($db, $_SESSION['userId']);
    return $user['name'] === 'Admin';
}

function createEvent($db, $artist, $venue, $event_date, $description, $price, $tickets_left, $image_url = null) {
    $statement = $db->prepare("INSERT INTO events (artist, venue, event_date, description, price, tickets_left, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $statement->bind_param('ssssdis', $artist, $venue, $event_date, $description, $price, $tickets_left, $image_url);
    $statement->execute();
}

?>