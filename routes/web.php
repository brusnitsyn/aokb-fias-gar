<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $content = file_get_contents('../composer.json');
    $content = json_decode($content,true);
    $host = \request()->getSchemeAndHttpHost();

    return [
        'name' => $content['name'],
        'description' => $content['description'],
        'version' => $content['version'],
        'api' => "$host/api"
    ];
});
