<html>
    <head>
    </head>
    <body>
        <?php include_once '../styles/topbar.php'; ?>
        <div class="container">
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
                Current level: <input id="startLv" name="startLv" type="number" /><br>
                Desired level: <input id="endLv" name="endLv" type="number" /><br>
                Material Restrictions: 
                <select id="restRarity" name="restRarity">
                    <option value="" selected>[NONE]</option>
                    <option value="SR">SR</option>
                    <option value="S">S</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                </select>
                <select id="restSign" name="restSign">
                    <option value="" selected>[NONE]</option>
                    <option value="lt"><</option>
                    <option value="lte"><=</option>
                    <option value="eq">=</option>
                    <option value="gt">></option>
                    <option value="gte">>=</option>
                </select>
                <input id="restMax" name="restMax" type="number" />
                <br>
                <p>NOTE 1: Calculations for nightmares don't use C materials since those don't exist</p>
                <p>NOTE 2: Cost of obtaining B or C materials is omitted since this are obtained mainly on story quests.</p>
                <p>NOTE 3: Cost of obtaining SR materials is omitted since those can't be obtained in quests.</p>
                <p>NOTE 4: Gold needed to upgrade is considered in the cost.</p>
                <p>NOTE 5: Upgrade Sword C doesn't exist but gems give the same xp as Upgrade Shield C, so use them instead.</p>
            </form>
            <button onclick="calc();">Calc</button>
            <div id="resultTable"></div>
        </div>
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <p>Processing info...</p>
            </div>
        </div>

        <script>
            var modal = document.getElementById("myModal");

            function calc() {
              modal.style.display = "block";
              var xhr = new XMLHttpRequest();
              var form = document.getElementById('data');
              var formData = new FormData(form);

              xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                  document.getElementById('resultTable').innerHTML = xhr.responseText;
                  modal.style.display = "none";
                  return false;
                }
              };
              xhr.open('POST', 'procs');
              xhr.send(formData);
            }
        </script>
    </body>
</html>
