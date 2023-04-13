<?php
$pdo = new PDO('mysql:host=localhost;dbname=dofus_backup', 'root', 'root');

//set encoding to utf8
$pdo->exec('SET NAMES utf8');

$statement = $pdo->prepare('
    SELECT `di`.ankama_id as item_ankama_id, `di`.name as item_name, `di`.description as item_description, `di`.level as item_level, `ina`.name as nature_name, `it`.name as type_name, `ir`.id as recipe_id
    FROM default_item `di`
    INNER JOIN item_nature `ina` on `di`.item_nature_id = `ina`.id
    INNER JOIN item_type `it` on `di`.item_type_id = `it`.id
    INNER JOIN recipe `ir` on `di`.id = `ir`.item_id
    ');
$statement->execute();

$items = $statement->fetchAll(PDO::FETCH_ASSOC);

$arr = [];
foreach ($items as $item) {
    $recipe_lines_statement = $pdo->prepare('SELECT rl.quantity as quantity, di.ankama_id as item_ankama_id FROM dofus_backup.recipe_line rl INNER JOIN dofus_backup.default_item di on rl.item_id = di.id WHERE rl.recipe_id = :recipe_id');
    $recipe_lines_statement->execute(['recipe_id' => $item['recipe_id']]);
    $recipe_lines = $recipe_lines_statement->fetchAll(PDO::FETCH_ASSOC);
    foreach ($recipe_lines as $recipe_line) {
        $item['recipe_lines'][] = [
            'item_id' => $recipe_line['item_ankama_id'],
            'quantity' => $recipe_line['quantity'],
        ];
    }
    $row = [
        '_id' => $item['item_ankama_id'],
        'name' => $item['item_name'],
        'type' => $item['type_name'],
        'level' => $item['item_level'],
        'description' => $item['item_description'],
        'nature' => $item['nature_name'],
    ];
    if (!empty($item['recipe_lines'])) {
        $row['recipe'] = $item['recipe_lines'];
    }

    $arr[] = $row;
}

file_put_contents('src/DataFixtures/items.json', json_encode($arr, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));

$statement = $pdo->prepare('SELECT * FROM player_class');
$statement->execute();
$player_classes = $statement->fetchAll(PDO::FETCH_ASSOC);

$arr = [];
foreach ($player_classes as $player_class) {
    $arr[] = [
        '_id' => $player_class['ankama_id'],
        'name' => $player_class['name'],
        'description' => $player_class['description'],
    ];
}

file_put_contents('src/DataFixtures/player_classes.json', json_encode($arr, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));

//professions
$statement = $pdo->prepare('SELECT * FROM profession');
$statement->execute();
$professions = $statement->fetchAll(PDO::FETCH_ASSOC);

$arr = [];
foreach ($professions as $profession) {
    $statement = $pdo->prepare('SELECT * FROM dofus_backup.default_item WHERE profession_id = :profession_id');
    $statement->execute(['profession_id' => $profession['id']]);
    $items = $statement->fetchAll(PDO::FETCH_ASSOC);
    $harvests = [];
    foreach ($items as $item) {
        $harvests[] = [
            '_id' => $item['ankama_id'],
        ];
    }
    $arr[] = [
        '_id' => $profession['ankama_id'],
        'name' => $profession['name'],
        'description' => $profession['description'],
        'harvests' => $harvests,
    ];
}

file_put_contents('src/DataFixtures/professions.json', json_encode($arr, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));