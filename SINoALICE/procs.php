<?php
include_once '../db/dbmongo.php';

$proc = filter_input(INPUT_POST, 'proc');

//delete(['_id' => '5f187d7fc1d26'], 'SINoALICE', 'chapters');
switch ($proc) {
    case 'addChapter':

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

    default:
        break;
}

