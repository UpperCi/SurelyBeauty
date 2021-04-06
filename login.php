<?php
require_once "Includes/init.php";
?>
<!doctype html>
<html lang="en">
<head>
    <?= file_get_contents("Includes/html/head.html"); ?>
    <link rel="stylesheet" type="text/css" href="Includes/css/login.css"/>
    <title>Surelybeauty</title>
</head>
<body>
<form action="db.php" method="post">
    <div class="login-field">
        <label for="user">E-mail</label>
        <input id="user" type="text" value="" name="user" placeholder="e-mail">
    </div>
    <div class="login-field">
        <label for="pass">Wachtwoord</label>
        <input id="pass" type="password" value="" name="pass"
               placeholder="wachtwoord">
    </div>
    <div id="login-div">
        <input type="submit" name="submit" value="Log In">
    </div>
</form>
</body>
