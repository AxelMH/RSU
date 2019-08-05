<?php
$title = "MTG Deck Import";
?>
<html>
    <head>
        <link rel="stylesheet" href="/rsu/styles/mtgStyle.css" type="text/css">
        <title><?= $title ?></title>
    </head>
    <body>
        <?php include_once './styles/topbar.php'; ?>
        <div class="container">
            <?php
            session_start();

            if (!empty($_SESSION['message'])) {
                echo $_SESSION['message'];
                unset($_SESSION['message']);
            }
            ?>
            <form action = "./deck_events.php" method = "post">
                Deck name: <input type="text" name="deckname"/><br>
                Deck list: <textarea rows="5" cols="50" name="deck"></textarea>
                <input type = "submit" value = "submit" />
            </form>
        </div>
    </body>
</html>

