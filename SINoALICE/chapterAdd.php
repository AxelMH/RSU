<html>
    <head>
    </head>
    <body>
        <?php include_once '../styles/topbar.php'; ?>
        <div class="container">
            <?php
            if (!empty($doc)) {
                echo "$doc[event] ($doc[difficulty]) - $doc[chapter] added successfully to db.";
            }
            ?>
            <form type="submit" action="procs" method="post">
                <input id="proc" name="proc" type="hidden" value="addChapter" />
                Event: <input id="event" name="event" type="text" /><br>
                Mode: <input  id="mode" name="mode" type="text" /><br>
                Chapter: <input  id="chapter" name="chapter" type="text" /><br>
                AP: <input  id="ap" name="ap" type="number" /><br>
                Difficulty: <input  id="difficulty" name="difficulty" type="number" /><br>
                Exp: <input  id="exp" name="exp" type="number" /><br>
                Gold: <input  id="gold" name="gold" type="number" /><br>
                Drops: <textarea id="drops" name="drops" rows="6" cols="50"></textarea><br>
                <input type="submit" value="Submit">
            </form>
        </div>
    </body>
</html>
