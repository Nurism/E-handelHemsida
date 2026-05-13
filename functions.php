<?php

session_start();
ob_start();

// Load installed packages
require_once 'vendor/autoload.php';

// Load secrets from the file .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


/**
 * Connect to the MySQL database.
 *
 * @return mysqli
 */
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

/**
 * Store a status message in the user session.
 *
 * @param string $message
 * @return void
 */
function setMessage($message) {
    $_SESSION['message'] = $message;
}

/**
 * Show the current session message and remove it.
 *
 * @return void
 */
function getMessage() {
    if (isset($_SESSION['message'])){
        echo '<div id="status-popup" class="popup-message">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']);
    }
}

/**
 * Create a new user record.
 *
 * @param mysqli $db
 * @param string $name
 * @param string $email
 * @param string $password
 * @return void
 */
function createUser($db, $name, $email, $password) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $statement = $db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $statement->bind_param('sss', $name, $email, $hashedPassword);
    $statement->execute();
}

/**
 * Get a user by email address.
 *
 * @param mysqli $db
 * @param string $email
 * @return array|null
 */
function getUserByEmail($db, $email){
    $statement = $db->prepare("SELECT * FROM users WHERE email = ?");
    $statement->bind_param('s', $email);
    $statement->execute();
    $result = $statement->get_result();
    return $result->fetch_assoc();
}

/**
 * Get a user by ID.
 *
 * @param mysqli $db
 * @param int $userId
 * @return array|null
 */
function getUserById($db, $userId){
    $statement = $db->prepare("SELECT * FROM users WHERE id = ?");
    $statement->bind_param('i', $userId);
    $statement->execute();
    $result = $statement->get_result();
    return $result->fetch_assoc();
}

/**
 * Redirect to another page and set a session message.
 *
 * @param string $url
 * @param string $message
 * @return void
 */
function redirectWithMessage($url, $message){
    $_SESSION['message'] = $message;
    header("Location: " . $url);
    exit();
}

/**
 * Authenticate the user with email and password.
 *
 * @param mysqli $db
 * @param string $email
 * @param string $password
 * @return bool
 */
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

/**
 * Check whether a user is logged in.
 *
 * @return bool
 */
function isLoggedIn(){
    if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != TRUE) {
        return FALSE;
    } 

    return TRUE;
}

/**
 * Get all events from the database.
 *
 * @param mysqli $db
 * @return array
 */
function getEvents($db) {
    $result = $db->query("SELECT * FROM events ORDER BY event_date");
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get a single event by ID.
 *
 * @param mysqli $db
 * @param int $id
 * @return array|null
 */
function getEventById($db, $id) {
    $statement = $db->prepare("SELECT * FROM events WHERE id = ?");
    $statement->bind_param('i', $id);
    $statement->execute();
    $result = $statement->get_result();
    return $result->fetch_assoc();
}

/**
 * Add an event to the current cart.
 *
 * @param int $eventId
 * @param int $quantity
 * @return void
 */
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

/**
 * Get the current cart with event details.
 *
 * @param mysqli $db
 * @return array
 */
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

/**
 * Remove an item from the cart.
 *
 * @param int $eventId
 * @return void
 */
function removeFromCart($eventId) {
    if (isset($_SESSION['cart'][$eventId])) {
        unset($_SESSION['cart'][$eventId]);
    }
}

/**
 * Update the quantity of an event in the cart.
 *
 * @param mysqli $db
 * @param int $eventId
 * @param int $quantity
 * @return bool
 */
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

/**
 * Create an order from the cart and update ticket inventory.
 *
 * @param mysqli $db
 * @param int $userId
 * @param array $cart
 * @return int
 */
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

/**
 * Get all orders for a given user.
 *
 * @param mysqli $db
 * @param int $userId
 * @return array
 */
function getOrdersByUser($db, $userId) {
    $statement = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
    $statement->bind_param('i', $userId);
    $statement->execute();
    $result = $statement->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get order items for a given order.
 *
 * @param mysqli $db
 * @param int $orderId
 * @return array
 */
function getOrderItems($db, $orderId) {
    $statement = $db->prepare(
        "SELECT oi.*, e.artist, e.venue, e.image_url FROM order_items oi " .
        "LEFT JOIN events e ON oi.event_id = e.id WHERE oi.order_id = ?"
    );
    $statement->bind_param('i', $orderId);
    $statement->execute();
    $result = $statement->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * End the session and redirect to the home page.
 *
 * @return void
 */
function logout() {
    session_destroy();
    header("Location: index.php");
    exit();
}

/**
 * Check whether the current user is an administrator.
 *
 * @param mysqli $db
 * @return bool
 */
function isAdmin($db) {
    if (!isLoggedIn()) {
        return false;
    }
    $user = getUserById($db, $_SESSION['userId']);
    return $user['name'] === 'Admin';
}

/**
 * Create a new event.
 *
 * @param mysqli $db
 * @param string $artist
 * @param string $venue
 * @param string $event_date
 * @param string $description
 * @param float $price
 * @param int $tickets_left
 * @param string|null $image_url
 * @return void
 */
function createEvent($db, $artist, $venue, $event_date, $description, $price, $tickets_left, $image_url = null) {
    $statement = $db->prepare("INSERT INTO events (artist, venue, event_date, description, price, tickets_left, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $statement->bind_param('ssssdis', $artist, $venue, $event_date, $description, $price, $tickets_left, $image_url);
    $statement->execute();
}

?>