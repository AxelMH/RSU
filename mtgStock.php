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


        <script>
            var cards = [<?php
        $cards = find([], $db, 'cards', ['projection' => ['name' => 1]]);
        foreach ($cards as $card) {
            echo '"' . str_replace('"', '\\"', $card['name']) . '", ';
        }
        ?>];
                var sets={<?php
                        $sets = find([], $db, 'sets');
        foreach ($sets as $set) {
            echo "\"$set[code]\":\"$set[name]\",";
        }

                ?>};
            function autocomplete(inp, arr) {
                /*the autocomplete function takes two arguments,
                 the text field element and an array of possible autocompleted values:*/
                var currentFocus;
                /*execute a function when someone writes in the text field:*/
                inp.addEventListener("input", function (e) {
                    var a, b, i, val = this.value;
                    /*close any already open lists of autocompleted values*/
                    closeAllLists();
                    if (!val) {
                        return false;
                    }
                    currentFocus = -1;
                    /*create a DIV element that will contain the items (values):*/
                    a = document.createElement("DIV");
                    a.setAttribute("id", this.id + "autocomplete-list");
                    a.setAttribute("class", "autocomplete-items");
                    /*append the DIV element as a child of the autocomplete container:*/
                    this.parentNode.appendChild(a);
                    /*for each item in the array...*/
                    for (i = 0; i < arr.length; i++) {
                        /*check if the item starts with the same letters as the text field value:*/
                        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                            /*create a DIV element for each matching element:*/
                            b = document.createElement("DIV");
                            /*make the matching letters bold:*/
                            b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                            b.innerHTML += arr[i].substr(val.length);
                            /*insert a input field that will hold the current array item's value:*/
                            b.innerHTML += '<input type="hidden" value="' + arr[i] + '">';
                            /*execute a function when someone clicks on the item value (DIV element):*/
                            b.addEventListener("click", function (e) {
                                /*insert the value for the autocomplete text field:*/
                                inp.value = this.getElementsByTagName("input")[0].value;
                                /*close the list of autocompleted values,
                                 (or any other open lists of autocompleted values:*/
                                closeAllLists();
                            });
                            a.appendChild(b);
                        }
                    }
                });
                /*execute a function presses a key on the keyboard:*/
                inp.addEventListener("keydown", function (e) {
                    var x = document.getElementById(this.id + "autocomplete-list");
                    if (x)
                        x = x.getElementsByTagName("div");
                    if (e.keyCode == 40) {
                        /*If the arrow DOWN key is pressed,
                         increase the currentFocus variable:*/
                        currentFocus++;
                        /*and and make the current item more visible:*/
                        addActive(x);
                    } else if (e.keyCode == 38) { //up
                        /*If the arrow UP key is pressed,
                         decrease the currentFocus variable:*/
                        currentFocus--;
                        /*and and make the current item more visible:*/
                        addActive(x);
                    } else if (e.keyCode == 13) {
                        /*If the ENTER key is pressed, prevent the form from being submitted,*/
                        e.preventDefault();
                        if (currentFocus > -1) {
                            /*and simulate a click on the "active" item:*/
                            if (x)
                                x[currentFocus].click();
                        }
                    }
                });
                function addActive(x) {
                    /*a function to classify an item as "active":*/
                    if (!x)
                        return false;
                    /*start by removing the "active" class on all items:*/
                    removeActive(x);
                    if (currentFocus >= x.length)
                        currentFocus = 0;
                    if (currentFocus < 0)
                        currentFocus = (x.length - 1);
                    /*add class "autocomplete-active":*/
                    x[currentFocus].classList.add("autocomplete-active");
                }
                function removeActive(x) {
                    /*a function to remove the "active" class from all autocomplete items:*/
                    for (var i = 0; i < x.length; i++) {
                        x[i].classList.remove("autocomplete-active");
                    }
                }
                function closeAllLists(elmnt) {
                    /*close all autocomplete lists in the document,
                     except the one passed as an argument:*/
                    var x = document.getElementsByClassName("autocomplete-items");
                    for (var i = 0; i < x.length; i++) {
                        if (elmnt != x[i] && elmnt != inp) {
                            x[i].parentNode.removeChild(x[i]);
                        }
                    }
                }
                /*execute a function when someone clicks in the document:*/
                document.addEventListener("click", function (e) {
                    closeAllLists(e.target);
                });
            }

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