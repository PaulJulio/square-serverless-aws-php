<?php
require_once __DIR__ . '/../../vendor/autoload.php';
$inventory = new \SquareServerless\Inventory();
$body = $inventory->fetchKiosk();
echo json_encode($body);