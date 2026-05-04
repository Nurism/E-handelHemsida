<?php

$pageTitle = 'VenueNow';
include 'top.php';

$events = getEvents($db);

?>

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

<?php include 'bottom.php'; ?>