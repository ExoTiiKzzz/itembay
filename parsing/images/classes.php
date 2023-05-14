<?php
ini_set('max_execution_time', '6000');
//retrieve dofus api images

//get all classes
const API_URL = 'https://api.dofusdb.fr/breeds';
$classes = [];
$skip = 0;
$limit = 50;

do {
    $url = API_URL . '?$skip=' . $skip . '&$limit=' . $limit;
    echo $url . PHP_EOL;
    $json = file_get_contents($url);
    $data = json_decode($json, true);
    $classes = array_merge($classes, $data['data']);
    $skip += $limit;
} while (count($data['data']) > 0);

foreach ($classes as $class) {
    echo slug($class['shortName']['fr']) . PHP_EOL;
    $classId = $class['id'];
    $classUrl = 'https://dofusdb.fr/breeds/standing/' . slug($class['shortName']['fr']) . '-m.png';
    $image = file_get_contents($classUrl);
    if (!$image) {
        echo 'No image for ' . $class['shortName']['fr'] . PHP_EOL;
    }
    $classPath = 'classes/' . $classId . '.png';

    file_put_contents($classPath, file_get_contents($classUrl));
}

function slug(string $string): string
{
    $table = array(
        'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
        'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r', '/' => '-', ' ' => '-'
    );

    // -- Remove duplicated spaces
    $stripped = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $string);

    // -- Returns the slug
    return strtolower(strtr($string, $table));
}
