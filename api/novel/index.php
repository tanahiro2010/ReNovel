<?php
require_once '../../functions/app.php';
$NovelData = new Novel('../../db/database.json', 'novel');

header('Content-type: application/json');
echo $NovelData->fetch_novel($_GET['novel_id'] ?? null) ?? json_encode(array('Error' => 'please input novel_id.'), JSON_PRETTY_PRINT);