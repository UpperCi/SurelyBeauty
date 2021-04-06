<?php
# deze pagina werkt als een soort API om SQL-queries uit te voeren in JavaScript
# dat zorgt ervoor dat je de pagina niet hoeft te herladen met elke query
# indien de query iets returnt wordt dit als JSON uitgeprint voor de JS
require_once "Includes/init.php";
require_once "Includes/loginValidation.php";

function returnIfValid($connection, $query)
{
    $result = $connection->query($query)
        ->fetchAll(PDO::FETCH_ASSOC);
    if ($result != FALSE) {
        return $result;
    }
    return false;
}

/* type queries
 * 0 -> haal afspraken op per dag [admin]
 * 1 -> verwijder een openingstijd-element [admin] | tId = integer
 * 2 -> haal openingstijden specifieke dag op | d = date-string
 * 3 -> haal per dag van maand op of SB die dag open is | d = date-string
 * 4 -> update status van specifieke afspraak [admin] | status = integer, id = integer
 * 5 -> voeg een admin_account toe [admin+] | newU = string, newP = string
 * 6 -> voeg nieuwe openingstijd toe [admin] | start = integer, end = integer
 * 7 -> voeg meerdere openingstijden toe in één keer [admin] | start = integer, end = integer, rCount = integer, rType = char
 * 8 -> verwijder alle openingstijden van één dag [admin] | start = date-string
 * */

if (isset($_GET['t'])) {
    switch ($_GET['t']) {
        case 0: // haal afspraken op
            if (checkLogin($connection, $_GET)) {
                $startTime = strtotime($_GET['d']);
                if (!is_integer($startTime)) break;
                $endTime = $startTime + 86400;

                $statement = $connection->prepare
                ("SELECT * FROM afspraken WHERE start > :start AND end < :end AND status = 1 ORDER BY start");
                $statement->execute([
                    ':start' => $startTime,
                    ':end' => $endTime
                ]);
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($result);
            }
            break;
        case 1: // verwijder timeslot
            if (checkLogin($connection, $_GET)) {
                $timeId = intval($_GET['tId']);
                if (is_numeric($timeId)) {
                    $statement = $connection->prepare(
                        "DELETE FROM openingstijden WHERE id=:id");
                    $statement->execute([':id' => $timeId]);
                }
            }
            break;
        case 2: // json openingstijden van specifieke dag (_get[d])
            $startTime = strtotime($_GET['d']);
            if (!is_integer($startTime)) break;
            $endTime = $startTime + 86400;

            $statement = $connection->prepare
            ("SELECT * FROM openingstijden WHERE start > :start AND end < :end");
            $statement->execute([
                ':start' => $startTime,
                ':end' => $endTime
            ]);
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($result);
            break;
        case 3: // haal in json een true/false array op die per dag van maand aangeeft of er die dag openingstijden zijn
            $baseTime = strtotime($_GET['d']);

            $isOpen = [];

            for ($i = 0; $i < 32; $i++) {
                $startTime = $baseTime + 86400 * $i;
                $endTime = $startTime + 86400;
                $statement = $connection->prepare
                ("SELECT * FROM openingstijden WHERE start > :start AND end < :end");
                $statement->execute([
                    ':start' => $startTime,
                    ':end' => $endTime
                ]);
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                if (count($result) > 0) {
                    array_push($isOpen, true);
                } else {
                    array_push($isOpen, false);
                }
            }
            echo json_encode($isOpen);
            break;
        case 4: // pas status van een afspraak aan
            if (checkLogin($connection, $_GET)) {

                $status = intval($_GET['status']);
                $id = intval($_GET['id']);

                $query = "UPDATE `afspraken` SET status = :status WHERE id = :id";
                $statement = $connection->prepare($query);
                $statement->execute([
                    ":status" => $status,
                    ":id" => $id
                ]);

            }
            break;
        case 5: // voeg admin-account toe, login-account heeft add_accounts nodig
            if (checkAdmin($connection, $_GET)) {
                if (!filter_var($_GET['e-mail'], FILTER_VALIDATE_EMAIL)) break;
                $newUser = $_GET['newU'];
                $newPass = password_hash($_GET['newP'], PASSWORD_BCRYPT, ['cost' => 12]);

                $statement = $connection->prepare("INSERT INTO admin_accounts VALUES 
                (NULL, :user, :pass, 0)");
                $statement->execute([':user' => $newUser, ':pass' => $newPass]);
            }
            break;
        case 6: // voeg een timeslot toe
            if (checkLogin($connection, $_GET)) {
                $start = intval($_GET['start']);
                $end = intval($_GET['end']);
                $statement = $connection->prepare(" INSERT INTO
                openingstijden (`id`, `start`, `end`) VALUES (NULL, :start, :end);");
                $statement->execute([
                    ':start' => $start,
                    ':end' => $end]);
            }
            break;
        case 7: // voeg een herhalende timeslot toe
            if (checkLogin($connection, $_GET)) {
                $start = intval($_GET['start']);
                $end = intval($_GET['end']);
                $statement = $connection->prepare(" INSERT INTO
                openingstijden (`id`, `start`, `end`) VALUES (NULL, :start, :end);");
                $repeatCount = intval($_GET['rCount']);
                switch ($_GET['rType']) {
                    case 'd': // herhaal elke dag
                        foreach (range(0, $repeatCount - 1) as $i) {
                            $timeStampAdd = $i * 86400;
                            $statement->execute([
                                ':start' => $start + $timeStampAdd,
                                ':end' => $end + $timeStampAdd
                            ]);
                        }
                        break;
                    case 'w': // herhaal elke week
                        foreach (range(0, $repeatCount - 1) as $i) {
                            $timeStampAdd = $i * 86400 * 7;
                            $statement->execute([
                                ':start' => $start + $timeStampAdd,
                                ':end' => $end + $timeStampAdd
                            ]);
                        }
                        break;
                    case 'm': // herhaal elke maand
                        foreach (range(0, $repeatCount - 1) as $i) {
                            $newStart = date_add(date_create($start), date_interval_create_from_date_string("{$i} months"));
                            $newEnd = date_add(date_create($end), date_interval_create_from_date_string("{$i} months"));
                            $statement->execute([
                                ':start' => $newStart,
                                ':end' => $newEnd
                            ]);
                        }
                        break;
                }
            }
            break;
        case 8: // verwijder timeslots van één dag
            if (checkLogin($connection, $_GET)) {
                $startTime = strtotime($_GET['d']);
                if (!is_integer($startTime)) break;
                $endTime = $startTime + 86400;

                $statement = $connection->prepare
                ("DELETE FROM openingstijden WHERE start > :start AND end < :end");
                $statement->execute([
                    ':start' => $startTime,
                    ':end' => $endTime
                ]);
                $statement = $connection->prepare
                ("DELETE FROM afspraken WHERE start > :start AND end < :end");
                $statement->execute([
                    ':start' => $startTime,
                    ':end' => $endTime
                ]);
            }
            break;
    }
}
