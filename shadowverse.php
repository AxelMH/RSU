<?php
include_once './db/dbmongo.php';

$filter = [];
$options = [];
$query = new MongoDB\Driver\Query($filter, $options);
$rows = $manager->executeQuery('foo.bar', $query);
$rows->setTypeMap(['root' => 'array', 'document' => 'array', 'array' => 'array']);

$result = $rows->toArray() ?? null;

error_log(__FILE__ . ' line ' . __LINE__ . ": " . print_r($result, true));


//try to write to db
$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);
$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
$bulk->insert(["_id" => uniqid(), "foo" => "bar2"]);

try {
    $result2 = $manager->executeBulkWrite('foo.bar', $bulk, $writeConcern);
    error_log(__FILE__ . ' line ' . __LINE__ . ": " . print_r($result2, true));
} catch (MongoDB\Driver\Exception\BulkWriteException $e) {
    $result2 = $e->getWriteResult();

    // Check if the write concern could not be fulfilled
    if ($writeConcernError = $result2->getWriteConcernError()) {
        error_log('ERROR MongoDB writeConcernError: ' . $writeConcernError->getMessage() . ' (' . $writeConcernError->getCode());
    }

    // Check if any write operations did not complete at all
    foreach ($result2->getWriteErrors() as $writeError) {
        error_log("ERROR MongoDB Operation #" . $writeError->getIndex() . ' ' . $writeError->getMessage() . ' (' . $writeError->getCode() . ')');
    }
} catch (MongoDB\Driver\Exception\Exception $e) {
    error_log("ERROR MongoDB Other: ", $e->getMessage());
}


//execute query again
$rows = $manager->executeQuery('foo.bar', $query);
$rows->setTypeMap(['root' => 'array', 'document' => 'array', 'array' => 'array']);

$result3 = $rows->toArray() ?? null;

error_log(__FILE__ . ' line ' . __LINE__ . ": " . print_r($result3, true));


die();
$deck = [
    "Mischievous Spirit" => 3,
    "Gremory" => 3,
    "Sonata of Silence" => 3,
    "Happy Pig" => 1,
    "Lady Grey, Deathweaver" => 3,
    "Ferry, Spirit Maiden" => 3,
    "Nicola, Forbidden Strength" => 3,
    "Fran, Monster Girl" => 3,
    "Orthrus, Junior Hellhound" => 3,
    "Zebet, Lady of the Flies" => 3,
    "Skull Ring" => 3,
    "Cerberus, Hound of Hades" => 3,
    "Gilnelise, Omen of Craving" => 3,
    "Arcus, Ghostly Manager" => 3,
];

$expansions = ["Altersphere", "Omen of the Ten", "Brigade of the Sky", "Dawnbreak, Nightedge", "Chronogenesis"];
$rarities = ['Bronze', 'Silver', 'Gold', 'Legendary'];

foreach ($expansions as $expansion) {
    foreach ($rarities as $rarity) {
        $usefullArray[$expansion][$rarity] = 0;
    }
}

$xml = simplexml_load_file("./db/shadowverse.xml");
$json = json_encode($xml);
$xmlArr = json_decode($json, true);

$cList = [];
foreach ($xmlArr['card'] as $card) {
    if (in_array($card['@attributes']['name'], array_keys($deck))) {
        addCard($card['@attributes'], $deck[$card['@attributes']['name']], $cList);
    }
}

function addCard($card, $qty, &$cList) {
    for ($i = 0; $i < $qty; $i++) {
        $cList[] = $card;
    }
}

foreach ($cList as $card) {
    $usefullArray[$card['expansion']][$card['rarity']] ++;
}
?>
<html>
    <head>
        <title>Shadowverse Stuffy</title>
    </head>
    <body>
        <h1>Shadowverse Stuffy</h1>
        <table>
            <?php
            foreach ($deck as $name => $qty) {
                echo '<tr>';
                echo '<td>' . $name . '</td>';
                echo '<td>' . $qty . '</td>';
                echo '</tr>';
            }
            ?>
        </table>
        <table>
            <?php
            echo '<tr>';
            echo '<td></td>';
            foreach ($rarities as $value) {
                echo '<td>' . $value . '</td>';
            }
            echo '</tr>';
            foreach ($usefullArray as $expansion => $rarity) {
                echo '<tr>';
                echo '<td>' . $expansion . '</td>';
                foreach ($rarity as $qty) {
                    echo '<td>' . $qty . '</td>';
                }
                echo '</tr>';
            }
            ?>
        </table>
    </body>
</html>
