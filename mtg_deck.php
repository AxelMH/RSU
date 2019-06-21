<?php
include_once './db/dbmongo.php';
include_once './db/mongoFunctions.php';
$dbName = 'MTG';

$decks = find([], $dbName, 'decks', ['projection' => ['deckname' => 1]]);
$deckId = filter_input(INPUT_POST, 'deck');
$title = 'Deck';
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
                $deck = findOne(['_id' => $deckId], $dbName, 'decks');

                echo "<h1>$deck[deckname]</h1>\n";
                foreach ($deck['deck'] as $cardname => $qty) {
                    echo $qty . "x $cardname<br>\n";
                }
            }
            ?>
        </div>
    </body>
</html>