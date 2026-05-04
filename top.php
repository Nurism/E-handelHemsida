<?php
require_once 'functions.php';
$db = connectToDb();
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/main.css" rel="stylesheet">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'VenueNow'; ?></title>
</head>
<body>
    <header>
        <h1><a href="index.php">VenueNow</a></h1>
        <nav>
            <?php if (isset($extraNav)) echo $extraNav; ?>
            <?php if (isLoggedIn()): ?>
                <span>Välkommen, <?= htmlspecialchars(getUserById($db, $_SESSION['userId'])['name']) ?>!</span>
                <a href="cart.php">Kundvagn</a>
                <?php if (isAdmin($db)): ?>
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