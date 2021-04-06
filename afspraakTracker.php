<?php
require_once "Includes/init.php";
//print_r($_GET['af']);
$info = getByTrackId($_GET['af'], $connection)[0];
$date = date('j F Y', $info['start']);
$start = date('H:i', $info['start']);
$end = date('H:i', $info['end']);

$behandelingen = getBehandelingen($connection, $info['behandel_id'], 'name');

$ICSurl = createAfspraakICS($connection, $info);
?>
<!doctype html>
<html lang="en">
<head>
    <?= file_get_contents("Includes/html/head.html"); ?>
    <link rel="stylesheet" type="text/css" href="Includes/css/tracker.css"/>
    <title>Surely Beauty</title>
</head>
<body>
<main>
    <h1>Uw Afspraak</h1>
    <div id="afspraak-card">
        <div id="afspraak-tijd">
            <h3><?= $date; ?></h3>
            <h2><?= "$start - $end" ?></h2>
        </div>
        <div id="afspraak-status">
            <h2><?= getStatus($info['status']) ?></h2>
        </div>
        <div id="afspraak-behandelingen">
            <h3>Behandelingen:</h3>
            <ul>
                <?php foreach ($behandelingen as $behandeling) { ?>
                    <li><?= $behandeling; ?></li>
                <?php } ?>
            </ul>
        </div>
        <div id="afspraak-contact">
            <div>
                <p>E-mail</p>
                <p>Tel</p>
            </div>
            <div>
                <p><?= htmlentities($info['email']) ?></p>
                <p><?= htmlentities($info['tel']) ?></p>
            </div>
        </div>
        <div id="afspraak-download">
            <a href="<?= $ICSurl ?>"><i class="far fa-calendar-alt"></i> Zet in
                Agenda</a>
        </div>
    </div>
</main>

<!--<footer>-->
<!--    --><? //= file_get_contents("Includes/footer.html"); ?>
<!--</footer>-->
</body>
