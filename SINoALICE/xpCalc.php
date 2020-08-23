<html>
    <head>
    </head>
    <body>
        <?php include_once '../styles/topbar.php'; ?>
        <div class="container">
            <!--<form type="submit" action="procs" method="post">-->
            <form id="data">
                <input id="proc" name="proc" type="hidden" value="calcXP" />
                Type: <select id="type" name="type">
                    <option value="weapon">Weapon</option>
                    <option value="armor">Armor</option>
                    <option value="nightmare">Nightmare</option>
                </select><br>
                Rarity: <select id="rarity" name="rarity">
                    <option value="L">L</option>
                    <option value="SR">SR</option>
                    <option value="S">S</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                </select><br>
                Current level: <input id="startLv" name="startLv" type="text" /><br>
                Desired level: <input id="endLv" name="endLv" type="text" /><br>
                <p>NOTE: Materials used for calculations are the ones that are easily obtained and are as follows:<br>
                    Weapon: S, A, B<br>
                    Armor: S, A, B, C<br>
                    Nightmare: A, B</p>

                <!--<input type="submit" value="Submit">-->
            </form>
            <button onclick="calc();">Calc</button>
            <div id="resultTable"></div>
        </div>
        <script>
            function calc() {
              var xhr = new XMLHttpRequest();
              var form = document.getElementById('data');
              var formData = new FormData(form);

              xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                  document.getElementById('resultTable').innerHTML = xhr.responseText;
                  return false;
                }
              };
              xhr.open('POST', 'procs');
              xhr.send(formData);
            }
        </script>
    </body>
</html>
