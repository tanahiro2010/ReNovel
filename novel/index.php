<?php
require_once '../functions/app.php';
require_once '../functions/header.php';

$Account = new DataBase('../db/database.json', 'novel');
$Novel = new Novel('../db/database.json', 'novel');

$user_data = $Account->isLogin();

echo_header($user_data);

$novel_id = null;
$author_id = null;

$novel_title = null;
$novel_description = null;
$novel_episodes = null;

$episode_title = "";
$episode_text = "";

$nextPageToken = "";
$lastPageToken = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["novel"])) {
        $novel_id = $_GET["novel"];

    }
}