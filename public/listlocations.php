<?php
require_once __DIR__ . '/../vendor/autoload.php';

$locations = new \SquareServerless\Locations();
echo json_encode([
    'locations' => $locations->getAsArray(),
    'errors' => $locations->getError(),
], JSON_PRETTY_PRINT);