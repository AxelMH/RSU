<?php

$proc = filter_input(INPUT_POST, 'proc');
switch ($proc) {
    case 'addChapter':
        include_once '../db/dbmongo.php';

        $doc = [
            "_id" => uniqid(),
            "event" => filter_input(INPUT_POST, 'event'),
            "mode" => filter_input(INPUT_POST, 'mode'),
            "chapter" => filter_input(INPUT_POST, 'chapter'),
            "ap" => filter_input(INPUT_POST, 'ap'),
            "exp" => filter_input(INPUT_POST, 'exp'),
            "gold" => filter_input(INPUT_POST, 'gold'),
            "difficulty" => filter_input(INPUT_POST, 'difficulty'),
            "drops" => array_filter(explode(PHP_EOL, trim(filter_input(INPUT_POST, 'drops')))),
        ];

        save($doc, 'SINoALICE', 'chapters');
        header("Location: chapterAdd");
        break;

    case 'addNightmare':
        include_once '../db/dbmongo.php';

        $doc = [
            "_id" => uniqid(),
            "name" => filter_input(INPUT_POST, 'mode'),
            "attribute" => filter_input(INPUT_POST, 'mode'),
            "grade" => filter_input(INPUT_POST, 'event'),
            "patk" => filter_input(INPUT_POST, 'chapter'),
            "matk" => filter_input(INPUT_POST, 'ap'),
            "pdef" => filter_input(INPUT_POST, 'exp'),
            "mdef" => filter_input(INPUT_POST, 'gold'),
            "evolve" => filter_input(INPUT_POST, 'difficulty'),
            "storySkill" => filter_input(INPUT_POST, 'difficulty'),
            "storySkillLv" => filter_input(INPUT_POST, 'difficulty'),
            "colosseumSkill" => filter_input(INPUT_POST, 'difficulty'),
            "colosseumSkillLv" => filter_input(INPUT_POST, 'difficulty'),
            "premium" => filter_input(INPUT_POST, 'gold'),
        ];

        save($doc, 'SINoALICE', 'nightmares');
        header("Location: chapterAdd");
        break;

    case 'calcXP':
        $xpArray = [1 => 14, 2 => 29, 3 => 42, 4 => 63, 5 => 76, 6 => 91, 7 => 109, 8 => 131, 9 => 157, 10 => 188, 11 => 207, 12 => 228, 13 => 251, 14 => 276, 15 => 304, 16 => 334, 17 => 367, 18 => 404, 19 => 485, 20 => 509, 21 => 534, 22 => 561, 23 => 589, 24 => 618, 25 => 649, 26 => 681, 27 => 715, 28 => 751, 29 => 901, 30 => 937, 31 => 974, 32 => 1013, 33 => 1054, 34 => 1096, 35 => 1140, 36 => 1186, 37 => 1233, 38 => 1282, 39 => 1538, 40 => 1584, 41 => 1632, 42 => 1681, 43 => 1731, 44 => 1783, 45 => 1836, 46 => 1891, 47 => 1948, 48 => 2006, 49 => 2407, 50 => 2479, 51 => 2553, 52 => 2630, 53 => 2709, 54 => 2790, 55 => 2874, 56 => 2960, 57 => 3049, 58 => 3140, 59 => 3768, 60 => 3806, 61 => 3844, 62 => 3882, 63 => 3921, 64 => 3960, 65 => 4000, 66 => 4040, 67 => 4080, 68 => 4121, 69 => 4945, 70 => 4994, 71 => 5044, 72 => 5094, 73 => 5145, 74 => 5196, 75 => 5248, 76 => 5300, 77 => 5353, 78 => 5407, 79 => 6488, 80 => 6520, 81 => 6553, 82 => 6586, 83 => 6619, 84 => 6652, 85 => 6685, 86 => 6718, 87 => 6752, 88 => 6786, 89 => 8143];
        $matsXp = ['C' => 50, 'B' => 200, 'A' => 1600, 'S' => 5500, 'SR' => 10000,];

        $rarityBase = ['B' => 500, 'A' => 750, 'S' => 1000, 'SR' => 1250, 'L' => 1500];
        $rarityCostPerLevel = ['B' => 5, 'A' => 10, 'S' => 20, 'SR' => 30, 'L' => 60];

        $missionsStats = [
//        'Beginner' => ['B' => 1, 'A' => 1, 'AP' => 15, 'Gold' => 900],
            'Intermediate' => ['A' => 3.41139240506329, 'AP' => 30, 'Gold' => 1800],
            'Advanced' => ['S' => 1.10493827160494, 'A' => 1.20987654320988, 'AP' => 45, 'Gold' => 2700],
        ];

        //not use C materials to calculate nigtmares
        $type = filter_input(INPUT_POST, 'type');
        if ($type == 'nightmare') {
            unset($matsXp['C']);
        }

        $startLv = filter_input(INPUT_POST, 'startLv');
        $endLv = filter_input(INPUT_POST, 'endLv');


        $neededXp = 0;

        for ($lv = $startLv; $lv < $endLv; $lv++) {
            $neededXp += $xpArray[$lv];
        }
        $rarity = filter_input(INPUT_POST, 'rarity');

        //get all possible combinations to get xp needed
        $combinations = [];
        getCombinations($matsXp, $neededXp);

        $remove = [];

        $restRarityArr = filter_input(INPUT_POST, 'restRarity', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $restSignArr = filter_input(INPUT_POST, 'restSign', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $restMaxArr = filter_input(INPUT_POST, 'restMax', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        foreach ($combinations as $key => $combination) {

            //apply restriction
            foreach (array_keys($restRarityArr) as $restNum) {
                $restRarity = $restRarityArr[$restNum];
                $restSign = $restSignArr[$restNum];
                if (!empty($restRarity) && !empty($restSign)) {
                    $restMax = $restMaxArr[$restNum];
                    switch ($restSign) {
                        case 'lt':
                            if ($combination[$restRarity] >= $restMax) {
                                $remove[] = $key;
                                continue 2;
                            }
                            break;
                        case 'lte':
                            if ($combination[$restRarity] > $restMax) {
                                $remove[] = $key;
                                continue 2;
                            }
                            break;
                        case 'eq':
                            if ($combination[$restRarity] != $restMax) {
                                $remove[] = $key;
                                continue 2;
                            }
                            break;
                        case 'gt':
                            if ($combination[$restRarity] <= $restMax) {
                                $remove[] = $key;
                                continue 2;
                            }
                            break;
                        case 'gte':
                            if ($combination[$restRarity] < $restMax) {
                                $remove[] = $key;
                                continue 2;
                            }
                            break;

                        default:
                            break;
                    }
                }
            }
            //Calculate AP
            $AP = 0;
            $level = $startLv;
            $money = getMoney($combination, $rarity, $level);

            if ($type != 'nightmare') {
                $missions = getMissions($combination);
                $materialsAP = ($missions['Advanced'] * $missionsStats['Advanced']['AP']) +
                        ($missions['Intermediate'] * $missionsStats['Intermediate']['AP']);

                $money -= ($missions['Advanced'] * $missionsStats['Advanced']['Gold']) +
                        ($missions['Intermediate'] * $missionsStats['Intermediate']['Gold']);
            } else {
                $materialsAP = 0;
            }
            $goldAP = 45 * $money / 100000;
            $totalAP = $goldAP + $materialsAP;
            $combinations[$key]['Materials AP'] = round($materialsAP, 4);
            $combinations[$key]['Gold AP'] = round($goldAP, 4);
            $combinations[$key]['Total AP Cost'] = round($totalAP, 4);
        }

        //remove things with more than 100 materials
        foreach ($remove as $key) {
            unset($combinations[$key]);
        }

        //sort results
        function usortTest($a, $b) {
            if ($a['Total AP Cost'] > $b['Total AP Cost']) {
                return 1;
            }
            return -1;
        }

        usort($combinations, "usortTest");

        //echo results
        $keys = array_keys($combinations[0]);
        echo'<table>';
        echo'<tr>';
        foreach ($keys as $head) {
            echo "<th>$head</th>";
        }
        echo'</tr>';

        for ($i = 0; $i < 200; $i++) {
            if (empty($combinations[$i])) {
                break;
            }
            echo'<tr>';
            foreach ($keys as $index) {
                echo'<td>' . $combinations[$i][$index] . '</td>';
            }
            echo'</tr>';
        }
        echo'</table>';
        die();
        break;

    default:
        break;
}

function getCombinations(array $values, int $neededValue, array $combination = []) {
    arsort($values);
    $rarity = array_key_first($values);
    $xp = array_shift($values);


    $max = ceil($neededValue / $xp);
    if (!empty($values)) {
        for ($i = 0; $i <= $max; $i++) {
            $combination[$rarity] = $i;
            getCombinations($values, ($neededValue - ($xp * $i)), $combination);
        }
    } else {
        global $combinations;
        $combination[$rarity] = max([$max, 0]);
        //only save combinations that have less than 200 materials to optimize memory
        if (array_sum($combination) < 200) {
            $combinations[] = $combination;
        }
    }
}

function getMoney(array $materials, string $ratity, int $level) {
    global $rarityBase, $rarityCostPerLevel;

    $combinationSplit = splitCombination($materials);
    $neededGold = 0;

    foreach ($combinationSplit as $key => $comb) {
        $neededGold += array_sum($comb) *
                ($rarityBase[$ratity] + ($rarityCostPerLevel[$ratity] * $level));

        //update level
        if ($key != (sizeof($combinationSplit) - 1)) {
            $level = updateLevel($level, $comb);
        }
    }
    return $neededGold;
}

/**
 * 
 * @param array $materials
 * @return type
 */
function getMissions(array $materials) {
    global $missionsStats;

    $advanced = $materials['S'] / $missionsStats['Advanced']['S'];
    $materials['A'] = max([$materials['A'] - ($advanced * $missionsStats['Advanced']['A']), 0]);
    $intermediate = $materials['A'] / $missionsStats['Intermediate']['A'];

    return ['Intermediate' => $intermediate, 'Advanced' => $advanced];
}

/**
 * Split a combination into combinations with 20 or less items in them starting
 * from the least xp giving 
 */
function splitCombination(array $combination, array &$splitCombination = []) {
    global $matsXp;
    $rarities = array_keys($matsXp);

    if (array_sum($combination) > 20) {
        $newCombination = [];
        foreach ($rarities as $rarity) {

            $newCombination[$rarity] = 0;
            $newCombCount = array_sum($newCombination);
            if ($newCombCount == 20) {
                continue;
            }

            $max = 20 - $newCombCount;
            if ($combination[$rarity] > $max) {
                $newCombination[$rarity] = $max;
                $combination[$rarity] -= $max;
            } else {
                $newCombination[$rarity] = $combination[$rarity];
                $combination[$rarity] = 0;
            }
        }
        $splitCombination[] = $newCombination;
        splitCombination($combination, $splitCombination);
    } else {
        $splitCombination[] = $combination;
    }
    return $splitCombination;
}

/**
 * 
 * TODO: Reparar esto
 * @global type $xpArray
 * @global type $matsXp
 * @param int $level
 * @param array $materials
 * @return int
 */
function updateLevel(int $level, array $materials) {
    global $xpArray, $matsXp;
    //get all xp obtained from materials
    $totalXP = 0;
    foreach ($materials as $rarity => $qty) {
        $totalXP += $matsXp[$rarity] * $qty;
    }

    while ($totalXP > 0) {
        if (!isset($xpArray[$level])) {
            error_log(__FILE__ . ' line ' . __LINE__ . ': ' . print_r("got to level $level (???)", true));

            die();
        }
        $totalXP -= $xpArray[$level];
        if ($totalXP > 0) {
            $level++;
        }
    }
    return $level;
}
