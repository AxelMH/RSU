<?php
include_once './db/dbmongo.php';
include_once './db/mongoFunctions.php';
$dbName = 'MTG';

$decks = find([], $dbName, 'decks', ['projection' => ['deckname' => 1]]);
$deckId = filter_input(INPUT_POST, 'deck');
$title = 'Deck vs Stock';
?>
<html>
    <head>
        <link rel="stylesheet" href="/rsu/styles/mtgStyle.css" type="text/css">
        <title><?= $title ?></title>
    </head>
    <body>
        <?php
        include_once './styles/topbar.php';
        ?>
        <div class="container">

            <form name="deckForm" method="post" action="" >
                <select id="deck" name="deck">
                    <?php
                    foreach ($decks as $deck) {
                        echo "<option value='$deck[_id]'>$deck[deckname]</option>";
                    }
                    ?>
                </select>
                <input type = "submit" value = "submit" />
            </form>
            <?php
            if (!empty($deckId)) {
                echo "Cards needed to complete deck: <br>\n";

                $deck = findOne(['_id' => $deckId], $dbName, 'decks')['deck'];
                $stock = find([], $dbName, 'stock', ['sort' => ['cardname' => 1]]);

                $simplifiedStock = [];
                foreach ($stock as $cardInStock) {
                    if (!isset($simplifiedStock[$cardInStock['cardname']])) {
                        $simplifiedStock[$cardInStock['cardname']] = $cardInStock['qty'];
                    } else {
                        $simplifiedStock[$cardInStock['cardname']] += $cardInStock['qty'];
                    }
                }

                $actualStock = substractFromArray($simplifiedStock, $deck);
                $missing = substractFromArray($deck, $simplifiedStock);

                //sort by type or rarity before echoing//
                $completeMissing = find(['name' => ['$in' => array_keys($missing)]], $dbName, 'cards', ['sort' => ['types' => 1], 'projection' => ['types' => 1, 'name' => 1]]);
                $count = 0;
                foreach ($completeMissing as $card) {
                    $type = implode(' ', $card['types']);
                    if (empty($prevType) || $prevType != $type) {
                        echo "<br><b>$type</b><br>\n";
                    }
                    $prevType = $type;
                    $cardname = $card['name'];
                    echo "$missing[$cardname] $cardname <br>\n";
                    $count += $missing[$cardname];
                }
                echo "<br><b>Total: $count </b><br>\n";

                if (!empty($missing)) {
                    echo '<br>';
                    echo '<h2>Replacements used:</h2>';
                    echo '<table>';
                    foreach ($missing as $cardname => $qty) {
                        for ($i = 0; $i < $qty; $i++) {
                            echo '<tr><td>' . $cardname . '</td><td><input type="text" id="' . $cardname . '|' . $i . '"></td>';
                        }
                    }
                    echo '</table>';
                }
                ?>
                <script src="./includes/autocomplete.js"></script>
                <script>
                    var cards = [<?php
            foreach (array_keys($actualStock) as $card) {
                echo '"' . str_replace('"', '\\"', $card) . '", ';
            }
            ?>];

    <?php
    foreach ($missing as $cardname => $qty) {
        for ($i = 0; $i < $qty; $i++) {
            echo 'autocomplete(document.getElementById("' . $cardname . '|' . $i . '"), cards);' . "\n";
        }
    }
    ?>
                </script>
                <?php
            }

            /**
             * Return an array with the result of $array1[key]-$array2[key] for each key.
             * If result is less than 1, $resultArray[key] is unset.
             * @param array $array1
             * @param array $array2
             * @return array
             */
            function substractFromArray(array $array1, array $array2) {
                $resultArray = $array1;
                foreach ($array2 as $key => $value) {
                    if (isset($resultArray[$key])) {
                        $resultArray[$key] -= $value;
                    } else {
                        $resultArray[$key] = -$value;
                    }
                    if ($resultArray[$key] < 1) {
                        unset($resultArray[$key]);
                    }
                }
                return $resultArray;
            }
            ?>
        </div>
    </body>
</html>