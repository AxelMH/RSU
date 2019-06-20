<html>
    <title>

    </title>
    <body>
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
    </body>
</html>

