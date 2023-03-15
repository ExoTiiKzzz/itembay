<?php
ini_set('max_execution_time', '6000');
//retrieve dofus api images

$types = [
    'classes'       => [
        'url'       => 'classes',
        'property'  => 'maleImg'
    ],
    'consommables'         => [
        'url'       => 'consumables',
        'property'  => 'imgUrl'
    ],
    'equipements'          => [
        'url'       => 'equipments',
        'property'  => 'imgUrl'
    ],
    'ressources'           => [
        'url'       => 'resources',
        'property'  => 'imgUrl'
    ],
    'armes'                => [
        'url'       => 'weapons',
        'property'  => 'imgUrl'
    ],
];

$base_url = 'https://fr.dofus.dofapi.fr/';

foreach($types as $directory => $endpoint) {
    $url = $base_url . $endpoint['url'];
    $json = file_get_contents($url);
    $data = json_decode($json, true);
    foreach($data as $item) {
        $image_url = $item[$endpoint['property']];
        $image = file_get_contents($image_url);
        //save image in directory
        $filename = $directory . '/' . $item['_id'] . '.png';
        file_put_contents($filename, $image);
    }
}