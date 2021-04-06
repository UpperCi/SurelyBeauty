<?php
require_once "Includes/init.php";
$behandelingen = behandelAssoc($connection);
?>
<!doctype html>
<html lang="en">
<head>
    <?= file_get_contents("Includes/html/head.html"); ?>
    <link rel="stylesheet" type="text/css" href="Includes/css/style.css"/>
    <title>Surely Beauty</title>
</head>
<body>
<div id="header-img">
    <img src="assets\surely.png">
</div>
<main>
    <div id="location-bg">
        <div id="location-data">
            <div id="location-text" class="">
                <h1>Surely Beauty</h1>
                <p>Harp 26, Rotterdam </p>
                <h3>
                    3068HM
                </h3>
                <div id="location-btn">
                    <a href="afspraak.php">
                        <button>Maak een afspraak</button>
                    </a>
                </div>
            </div>
            <div id="location-img" class="">
                <img src="assets/sbPhoto.jpg" alt="foto Surelybeauty">
            </div>
        </div>
    </div>

    <div id="behandel-tekst">
        <h1>Behandelingen</h1>
    </div>

    <div id="prijslijst">
        <?php foreach ($behandelingen as $catNaam => $categorie) { ?>
            <button type="button" class="collapsible"><?= $catNaam ?></button>
            <div class="behandel-sectie">
                <?php foreach ($categorie as $behandeling) { ?>
                    <div class="behandeling">
                        <?= behandelHTML($behandeling); ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
    <div id="afspraak-div">
        <a href="afspraak.php">Maak een afpsraak</a>
    </div>
</main>
<footer>
    <?= file_get_contents("Includes/html/footer.html") ?>
</footer>
<script async defer src="Includes/JS/index.js"></script>
</body>
