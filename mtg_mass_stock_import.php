<?php
include_once './db/dbmongo.php';
include_once './db/mongoFunctions.php';

$db = 'MTG';
$title = "MTG List Stock Import";
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
            <p>Only insert cards that meet the same conditions (set and foil)</p>
            <form action = "./stock_events.php" method = "post">
                <input type="hidden" name="action" value="addList"/>                
                List: <textarea rows="5" cols="50" name="list"></textarea>
                <select id="printings" name="printings" style="width:200px">
                    <?php
                    $sets = find([], $db, 'sets', ['sort' => ['releaseDate' => -1]]);

                    foreach ($sets as $set) {
                        echo "<option value='$set[code]]'>$set[name]</option>";
                    }
                    ?>
                </select>                    
                Foil: <input id="foil" type="checkbox" name="foil">
                <input type = "submit" value = "submit" />
            </form>
        </div>
    </body>
</html>

