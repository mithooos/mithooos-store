<?php
require 'api/backend/includes/Database.php';
require 'api/backend/includes/Inventory.php';
\ = new Database('api/backend/database.sqlite');
\ = new Inventory(\);
echo \->getAvailableStock(1);
?>
