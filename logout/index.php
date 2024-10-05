<?php
require_once '../functions/app.php';

$Account = new DataBase('../db/database.json', 'novel');
$Account->logout();

header('Location: /');