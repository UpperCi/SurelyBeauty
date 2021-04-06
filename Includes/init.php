<?php
require_once "settings.php";
require_once "data/database.php";
require_once "formValid.php";
require_once "data/afspraakData.php";
require_once "data/behandelingData.php";

/** @var PDO $connection */
$connection = connectDatabase(DB_HOST, DB_USER, DB_PASS, DB_NAME);
