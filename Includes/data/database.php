<?php

/**
 * @param $host
 * @param $user
 * @param $pass
 * @param $dbname
 * @return PDO
 */
function connectDatabase($host, $user, $pass, $dbname): \PDO
{
    try {
        $connection = new \PDO("mysql:dbname=$dbname;host=$host", $user, $pass);
        $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $connection;
    } catch (\PDOException $e) {
        throw new \PDOException("DB connection failed: {$e->getMessage()}");
    }
}
