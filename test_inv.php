<?php
require 'api/backend/includes/Database.php';
require 'api/backend/includes/Inventory.php';
$db = new Database('api/backend/database.sqlite');
$inv = new Inventory($db);
echo $inv->getAvailableStock(1);
