<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = new \Calma\Mf\Application(__DIR__ . '/../', 'dev');
$app->run();