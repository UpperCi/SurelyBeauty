<?php
require_once "./Includes/ICS/ICSloader.php";

use Jsvrcek\ICS\Exception\CalendarEventException;
use Jsvrcek\ICS\Model\Calendar;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Utility\Formatter;
use Jsvrcek\ICS\CalendarStream;
use Jsvrcek\ICS\CalendarExport;

# haal alle afspraken met een bepaalde status op
/**
 * @param PDO $conn
 * @param int $status
 * @return array
 */
function afspraakAssoc(PDO $conn, int $status): array
{
    $statement = $conn->prepare("SELECT * FROM afspraken WHERE status=:status");
    $statement->execute([":status" => $status]);
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

# wordt in de admin-interface gebruikt voor de pending afspraken
/**
 * @param $af
 * @return string
 */
function quickAfspraakHTML($af): string
{
    $id = 'afs_' . $af['id'];
    $afHTML = "<div class='afspraak' id='{$id}'>";
    if (isset($af['start']) && isset($af['end'])) {
        $startStr = date("H:i", $af['start']);
        $endStr = date("H:i", $af['end']);
        $afHTML .= "<p class='afspraak-tijd'>{$startStr} - {$endStr}</p>";
    }
    if (isset($af['email'])) {
        $email = htmlentities($af['email']);
        $afHTML .= "<a class='afspraak-email' href='mailto:{$email}'><i class='far fa-envelope'></i></a>";
    }
    $afHTML .= "<input type='button' class='afspraak-accept' value='accepteren'>
                <input type='button' class='afspraak-deny' value='weigeren'>
                </div>";
    return $afHTML;
}

# recursive zodat je niet een id kan krijgen dat al bestaat
/**
 * @param PDO $connection
 * @return string
 */
function createTrackId(PDO $connection): string
{
    $characters = 'abcdefghijklmnopqrstuvwxyz';
    $randomId = '';
    for ($i = 0; $i < 6; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomId .= $characters[$index];
    }
    $result = $connection->query("SELECT * FROM afspraken WHERE tracker_id='{$randomId}'");
    if ($result != FALSE) return $randomId; # to-do: testen of dit überhaupt werkt
    else return createTrackId($connection);
}

/**
 * @param $id
 * @param PDO $connection
 * @return array|false
 */
function getByTrackId($id, PDO $connection)
{
    $statement = $connection->prepare("SELECT * FROM afspraken WHERE tracker_id=:trackId");
    $statement->execute([':trackId' => $id]);
    if ($statement->rowCount() == 1) {
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    } else return false;
}

# alles nodig om een afspraak aan de database toe te voegen in één functie!
/**
 * @param $af
 * @param PDO $connection
 * @return string
 */
function addAfspraak($af, PDO $connection): string
{
    $behandelingenRes = $af['behandelingen'];
    $behandelLength = 0;
    $start = $af['selectedTime'];
    foreach ($behandelingenRes as $behandelId) {
        $behandelId = intval($behandelId);
        $behandelData = $connection->query("SELECT * FROM behandelingen WHERE id={$behandelId}")
            ->fetchAll(PDO::FETCH_ASSOC)[0];
        if (!empty($behandelData['length'])) $behandelLength += intval($behandelData['length']);
    }
    $end = $start + $behandelLength * 60;

    # een manier om meerdere id's in één cell te doen gezien mySQL geen arrays heeft
    # arr(2, 5, 7) wordt "2_5_7"
    $behandelStr = implode('_', $behandelingenRes);
    $email = $af['e-mail'];
    $tracking = createTrackId($connection);

    $tel = filter_var($af['phone'], FILTER_SANITIZE_NUMBER_INT);
    $tel = str_replace("-", "", $tel);
    $tel = str_replace("+", "", $tel);

    $statement = $connection->prepare("INSERT INTO afspraken 
    VALUES (NULL, :start, :end, :b_id, :tr_id, :email, :tel, 0);");
    $result = $statement->execute([
        ':start' => $start,
        ':end' => $end,
        ':b_id' => $behandelStr,
        ':tr_id' => $tracking,
        ':email' => $email,
        ':tel' => $tel,
    ]);
    return $tracking;
}

# maak een ICS-bestand aan op basis van een afspraak
# wordt opgeslagen in Includes/private/data/{tracker_id}.ics
# https://github.com/jasvrcek/ICS gebruikt
/**
 * @param $conn
 * @param $af
 * @return string
 * @throws CalendarEventException
 */
function createAfspraakICS($conn, $af): string
{
    $start = new DateTime();
    $start->setTimestamp($af['start']);
    $end = new DateTime();
    $end->setTimestamp($af['end']);
    $behandelingen = getBehandelingen($conn, $af['behandel_id'], 'name');
    $desc = implode(', ', $behandelingen);

    $eventOne = new CalendarEvent();
    $eventOne->setStart($start)
        ->setEnd($end)
        ->setDescription($desc)
        ->setSummary('Afspraak Surely Beauty')
        ->setUid('event-uid');

    $calendar = new Calendar();
    $calendar->setProdId('//Surely Beauty//Afspraken//NL')
        ->addEvent($eventOne);

    $calendarExport = new CalendarExport(new CalendarStream, new Formatter());
    $calendarExport->addCalendar($calendar);

    $ICSurl = "Includes/private/data/{$af['tracker_id']}.ics";
    $trackFile = fopen($ICSurl, 'w') or die("unable to open file");
    fwrite($trackFile, $calendarExport->getStream());
    fclose($trackFile);

    return $ICSurl;
}

/**
 * @param $stat
 * @return string
 */
function getStatus($stat): string
{
    switch ($stat) {
        case 1:
            return "Geaccepteerd";
        case 2:
            return "Afgewezen";
        default:
            return "In Afwachting";
    }
}
