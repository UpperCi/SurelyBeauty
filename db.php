<?php
require_once "Includes/init.php";
require_once "Includes/loginValidation.php";
requireLogin($connection);
$pending = afspraakAssoc($connection, 0); // afspraken die nog geen definitieve status hebben
?>

<html lang="en">
<head>
    <?= file_get_contents("Includes/html/head.html"); ?>
    <link rel="stylesheet" type="text/css" href="Includes/css/admin.css"/>
    <title>Surely Beauty</title>
    <script>
        const USER = "<?= $_SESSION['user']; ?>";
        const PASS = "<?= $_SESSION['pass']; ?>";
    </script>
</head>
<body>
<main>
    <div id="kalender-container">
        <?= file_get_contents("Includes/html/calendar.html") ?>
    </div>

    <div id="afspraak-adder" class="adminComp">
        <div id="data-wrapper">
            <div id="adder-main">
                <label>
                    <input type="time" id="time-start">
                </label>
                <label>
                    <input type="time" id="time-end">
                </label>
                <label>
                    <input type="date" id="time-date">
                </label>
            </div>

            <div id="repeat-full">
                <label id="do-repeat-label" for="do-repeat">Herhaal</label>
                <input type="checkbox" id="do-repeat">

                <div id="repeat-div" style="display: none">
                    <label for="time-repeat-type" class="time-desc">elke</label>
                    <select id="time-repeat-type">
                        <option value="d">dag</option>
                        <option value="w">week</option>
                    </select>
                    <p class="time-desc">voor</p>
                    <input type="number" id="time-repeat-amount">
                    <label class="time-desc"
                           for="time-repeat-amount">keer</label>
                </div>

            </div>
        </div>


        <i class="fas fa-plus" id="afpsraak-adder-btn"></i>

    </div>
    <div id="afspraak-confirmation">

    </div>

    <div id="overzichten">
        <div id="afspraken" class="overzicht">

        </div>

        <div id="timeslot-overzicht" class="overzicht">

        </div>
    </div>

</main>
<aside>
    <?php if (count($pending) > 0): ?>
    <div id="afspraak-overzicht" class="adminComp">
        <div id="afspraak-pending">
            <h2>In Afwachting</h2>
            <div id="pending-content">
                <?php foreach ($pending as $afspraak) { ?>
                    <?= quickAfspraakHTML($afspraak); ?>
                <?php } ?>
            </div>
        </div>

    </div>
    <?php endif; ?>
</aside>


</body>

<script async defer src="Includes/JS/calendar.js"></script>
<script async defer src="Includes/JS/db.js"></script>
