<?php

$path = config_path('/acorn-favicons.json');
if (! file_exists($path)) {
    $path = __DIR__ . '/acorn-favicons.json';
}

return wp_json_file_decode($path, [
    'associative' => true,
]);
