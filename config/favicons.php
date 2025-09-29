<?php

$path = config_path('/favicons.json');
if (! file_exists($path)) {
    $path = __DIR__ . '/favicons.json';
}

return wp_json_file_decode($path, [
    'associative' => true,
]);
