<?php

# geef html terug om een behandeling direct uit te echo-en
/**
 * @param PDO $conn
 * @return array
 */
function behandelAssoc(PDO $conn)
{
    $behandelAssoc = [];
    $cats = $conn->query("SELECT id, name FROM categorieen ORDER BY display_order")->
    fetchAll(PDO::FETCH_ASSOC);
    # [['id':1,'name':'abcd']]
    foreach ($cats as $cat) {
        $cId = $cat['id'];
        $cName = $cat['name'];
        $statement = $conn->query("SELECT * FROM behandelingen WHERE cat={$cId}");
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $behandelAssoc[$cName] = $result;
    }
    return $behandelAssoc;
}

# return assoc-array of één waarde van behandeling op basis van ID
/**
 * @param PDO $conn
 * @param $id
 * @param false $sel
 * @return mixed
 */
function getBehandeling(PDO $conn, $id, $sel = false)
{
    $statement = $conn->prepare("SELECT * FROM behandelingen WHERE id=:id");
    $statement->execute([':id' => $id]);
    $result = $statement->fetchAll(PDO::FETCH_ASSOC)[0];
    if ($sel) {
        return $result[$sel];
    } else return $result;
}

# voer getBehandeling() uit op een string zoals deze in de database staat ("1_3_22")
/**
 * @param PDO $conn
 * @param $idStr
 * @param false $sel
 * @return array
 */
function getBehandelingen(PDO $conn, $idStr, $sel = false)
{
    $ids = explode('_', $idStr);
    $returnArr = [];
    foreach ($ids as $id) {
        array_push($returnArr, getBehandeling($conn, $id, $sel));
    }
    return $returnArr;
}

# html van een behandel-assoc-array om direct te echoën
/**
 * @param $b
 * @return string
 */
function behandelHTML($b)
{
    $bhtml = "<div> <h2 class='naam'>{$b['name']}</h2>";
    if (isset($b['length'])) {
        $bhtml .= "<h3 class='tijd'>{$b['length']} minuten</h3>";
    }
    if (isset($b['desc'])) {
        $bhtml .= "<p class='beschrijving'>{$b['desc']}</p>";
    }
    $bhtml .= "</div>";
    if (isset($b['price'])) {
        $bhtml .= "<h1 class='prijs'>€{$b['price']}</h1>";
    }
    return $bhtml;
}
