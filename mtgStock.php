<?php
session_start();
include './db/dbmongo.php';
include './db/mongoFunctions.php';

$db = 'MTG';
?>

<html>
    <style>
        * { box-sizing: border-box; }
        body {
            font: 16px Arial;
        }
        .autocomplete {
            /*the container must be positioned relative:*/
            position: relative;
            display: inline-block;
        }
        input {
            border: 1px solid transparent;
            background-color: #f1f1f1;
            padding: 10px;
            font-size: 16px;
        }
        input[type=text] {
            background-color: #f1f1f1;
            width: 100%;
        }
        input[type=submit] {
            background-color: DodgerBlue;
            color: #fff;
        }
        .autocomplete-items {
            position: absolute;
            border: 1px solid #d4d4d4;
            border-bottom: none;
            border-top: none;
            z-index: 99;
            /*position the autocomplete items to be the same width as the container:*/
            top: 100%;
            left: 0;
            right: 0;
        }
        .autocomplete-items div {
            padding: 10px;
            cursor: pointer;
            background-color: #fff;
            border-bottom: 1px solid #d4d4d4;
        }
        .autocomplete-items div:hover {
            /*when hovering an item:*/
            background-color: #e9e9e9;
        }
        .autocomplete-active {
            /*when navigating through the items using the arrow keys:*/
            background-color: DodgerBlue !important;
            color: #ffffff;
        }
    </style>
    <title></title>
    <body>
        <?php
        if (!empty($_SESSION['message'])) {
            echo $_SESSION['message'];
            unset($_SESSION['message']);
        }
        ?>
        <form autocomplete="off" action="./stock_events.php"  method="post">
            <div class="autocomplete" style="width:300px;">
                <input type="hidden" name="action" value="add"/>
                <input type="number" name="quantity" value="1" min="1"/>
                <input id="card" type="text" name="card" placeholder="card" onchange="updPrintings()"/><br/>
                <select id="printings" name="printings" style="width:200px"/>
                <br/>
                Foil: <input id="foil" type="checkbox" name="foil">
            </div>
            <input type="submit">
        </form>

        <script src="./includes/autocomplete.js"></script>
        <script>
            var cards = [<?php
        $cards = find([], $db, 'cards', ['projection' => ['name' => 1]]);
        foreach ($cards as $card) {
            echo '"' . str_replace('"', '\\"', $card['name']) . '", ';
        }
        ?>];
            var sets = {<?php
        $sets = find([], $db, 'sets');
        foreach ($sets as $set) {
            echo "\"$set[code]\":\"$set[name]\",";
        }
        ?>};

            autocomplete(document.getElementById("card"), cards);

            function updPrintings() {
                //apparently the autocomplete takes time so we use a timeout
                setTimeout(function () {

                    var FD = new FormData();
                    var XHR = new XMLHttpRequest();

                    FD.append('action', 'printings');
                    FD.append('card', document.getElementById('card').value);

                    XHR.onreadystatechange = function () {
                        if (XHR.readyState === 4 && XHR.status === 200) {
                            clearOptions();
                            populatePrintings(JSON.parse(XHR.responseText));
                        }
                    };

                    var url = "./stock_events.php";
                    XHR.open("POST", url, true);
                    XHR.send(FD);
                }, 150);

            }

            function clearOptions() {
                printingsObj = document.getElementById('printings');

                for (i = printingsObj.options.length - 1; i >= 0; i--) {
                    printingsObj.remove(i);
                }
            }

            function populatePrintings(values) {
                var sel = document.getElementById('printings');

                for (i = 0; i < values.length; i++) {
                    var opt = document.createElement('option');
                    opt.appendChild(document.createTextNode(sets[values[i]]));
                    opt.value = values[i];
                    sel.appendChild(opt);
                }
            }

        </script>

    </body>

</html>