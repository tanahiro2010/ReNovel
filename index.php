<?php
require_once './functions/app.php';
require_once './functions/header.php';
$DB = new DataBase('./db/database.json', 'novel');

$userData = $DB->isLogin();
echo_header($userData);
?>

<main>
    <section class="novels">

    </section>
</main>
