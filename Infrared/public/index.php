<?php

include __DIR__.'/../vendor/autoload.php';

use Infrared\App;

$config = json_decode(file_get_contents(__DIR__.'/../config.json'));

App::init($config)->run();