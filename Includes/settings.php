<?php
define("DB_HOST", "placeholder");
define("DB_USER", "placeholder");
define("DB_PASS", "placeholder");
define("DB_NAME", "placeholder");

set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, $severity, $severity, $file, $line);
});
