<html>
    <head>
    </head>
    <body>
        <?php include_once '../styles/topbar.php'; ?>
        <div class="container">
            <form id="data">
                <input id="proc" name="proc" type="hidden" value="calcXP" />
                <label>Type:</label><select id="type" name="type">
                    <option value="weapon">Weapon</option>
                    <option value="armor">Armor</option>
                    <option value="nightmare">Nightmare</option>
                </select><br>
                <label>Rarity:</label><select id="rarity" name="rarity">
                    <option value="L">L</option>
                    <option value="SR">SR</option>
                    <option value="S">S</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                </select><br>
                <label>Current level:</label><input id="startLv" name="startLv" type="number" /><br>
                <label>Desired level:</label><input id="endLv" name="endLv" type="number" /><br>
                <label>Material Restrictions:</label><button  type="button" onclick="addRestriction()">+</button>
                <div id="restrictions">
                    <div id="restriction">
                        <select id="restRarity" name="restRarity[]">
                            <option value="" selected>[NONE]</option>
                            <option value="SR">SR</option>
                            <option value="S">S</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                        </select>
                        <select id="restSign" name="restSign[]">
                            <option value="" selected>[NONE]</option>
                            <option value="lt"><</option>
                            <option value="lte"><=</option>
                            <option value="eq">=</option>
                            <option value="gt">></option>
                            <option value="gte">>=</option>
                        </select>
                        <input id="restMax" name="restMax[]" type="number" />
                    </div>
                </div>
            </form>
            <p>NOTES:</p>
            <ul>
                <li>Cost of obtaining B or C materials is omitted since this are obtained mainly on story quests.</li>
                <li>Cost of obtaining SR materials is omitted since those can't be obtained in quests.</li>
                <li>Calculations for nightmares don't use C materials since those don't exist.</li>
                <li>Upgrade Sword C doesn't exist but gems give the same xp as Upgrade Shield C, so use them instead.</li>
            </ul>
            <button onclick="calc();">Calculate</button>
            <div id="resultTable"></div>
        </div>

        <script>
            function calc() {
              processModal.style.display = "block";
              var xhr = new XMLHttpRequest();
              var form = document.getElementById('data');
              var formData = new FormData(form);

              xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                  document.getElementById('resultTable').innerHTML = xhr.responseText;
                  processModal.style.display = "none";
                  return false;
                }
              };
              xhr.open('POST', 'procs');
              xhr.send(formData);
            }

            function addRestriction() {
              restriction = document.getElementById('restriction');
              var cln = restriction.cloneNode(true);
              document.getElementById('restrictions').appendChild(cln);
            }
        </script>
    </body>
</html>
