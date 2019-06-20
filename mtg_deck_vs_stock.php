<?php
include_once './db/dbmongo.php';
include_once './db/mongoFunctions.php';
$dbName = 'MTG';

$decks = find([], $dbName, 'decks', ['projection' => ['deckname' => 1]]);
$deckId = filter_input(INPUT_POST, 'deck');
?>
<html>
    <title>Deck vs Stock</title>
    <body>
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

            foreach ($deck as $cardname => $qty) {
                $cardStock = findOne(['cardname' => $cardname], $dbName, 'stock', ['projection' => ['qty' => 1]]);
                
                if (!empty($cardStock['qty'])) {
                    $qty -= $cardStock['qty'];
                }
                if ($qty > 0) {
                    echo "$qty $cardname <br>\n";
                }
            }
        }
        ?>
    </body>
</html>