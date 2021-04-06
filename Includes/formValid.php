<?php
/* selectedTime -> time
 * behandelingen -> arr
 * e-mail
 * phone*/

// valideert van een afspraak de tijden, behandelingen, email en telefoonnummer
// returnt assoc-arr met errors
/**
 * @param array $res
 * @return array
 */
function validateAfspraak(array $res): array
{
    $errs = [];

    if (empty($res['selectedTime']) || !isset($res['selectedTime'])) {
        $errs['time'] = "Selecteer een geldige tijd";
    } else if ($res['selectedTime'] < 0 || $res['selectedTime'] > 4294967295) {
        $errs['time'] = "Selecteer een geldige tijd";
    }

    if (empty($res['behandelingen'])) {
        $errs['behandelingen'] = "Selecteer één of meer geldige behandelingen";
    } else if (count($res['behandelingen']) > 10) {
        $errs['behandelingen'] = "Je kan niet meer dan 10 behandelingen selecteren";
    } else {
        foreach ($res['behandelingen'] as $key => $behandeling) {
            $res['behandelingen'][$key] = min(intval($behandeling), 9999);
        }
    }

    if (empty($res['e-mail'])) {
        $errs['email'] = "Voer een geldig e-mailadres in";
    } else if (!filter_var($res['e-mail'], FILTER_VALIDATE_EMAIL)) {
        $errs['email'] = "E-mailadres is ongeldig";
    }
    // thx stackOverflow
    if (empty($res['phone'])) {
        $errs['phone'] = "Voer een geldig telefoonnummer in";
    }
    $filtered_phone_number = filter_var($res['phone'], FILTER_SANITIZE_NUMBER_INT);
    $phone_to_check = str_replace("-", "", $filtered_phone_number);
    if (strlen($phone_to_check) < 10 || strlen($phone_to_check) > 15) {
        $errs['phone'] = "Ongeldig Telefoonnummer";
    }
    return $errs;
}

// indien $arr het attribuut $i bevat, geeft dat terug. Geef anders een lege string
// makkelijk om arrays direct uit te printen zonder steeds isset() te moeten roepen
/**
 * @param $arr
 * @param $i
 * @return string
 */
function arrIfSet($arr, $i): string
{
    if (isset($arr[$i])) return $arr[$i];
    else return "";
}
