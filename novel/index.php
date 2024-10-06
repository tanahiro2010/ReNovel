<?php
require_once '../functions/app.php';
require_once '../functions/header.php';

$Account = new DataBase('../db/database.json', 'novel');
$Novel = new Novel('../db/database.json', 'novel');

$user_data = $Account->isLogin();

echo_header($user_data);

$novel_id = null;
$author_id = null;

$novel_title = "";
$novel_description = "";
$novel_episodes = array();
$novel_update_date = "";

$episode_id = "";
$episode_title = "";
$episode_text = "";
$episode_update_date = "";

$nextPageToken = "";
$lastPageToken = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["novel"])) {
        $novel_id = $_GET["novel"];
        $novel_data = $Novel->fetch_novel($novel_id);

        if (!$novel_data || $novel_data['status'] == 'private') {
            header('Location: /');
            exit();
        }

        echo '<main class="p-4 h-screen w-screen flex justify-center items-center">';

        $novel_title = $novel_data['title'];
        $novel_description = $novel_data['description'];
        $novel_update_date = $novel_data['last_update'];
        $novel_episodes = $novel_data['episodes'] ?? array();

        ?>

        <section class="novel-content-show mb-6 p-4 border border-gray-200 rounded shadow-md cursor-pointer" onclick="location.href = './?novel=<?php echo $novel_id; ?>';">
            <h2 class="section-title text-2xl font-bold text-center"><?php echo $novel_title; ?></h2>
            <p class="mt-2 text-center">作者: <a href="/user?user=<?php echo $novel_data['author_id']; ?>" class="link-button text-blue-500 hover:underline"><?php echo $Account->fetch($novel_data['author_id'], 'name'); ?></a></p>
            <div class="section-description mt-4 text-center">
                <h3 class="font-semibold">紹介文</h3>
                <p><?php echo all_convert($novel_description); ?></p>
            </div>
        </section>

        <?php

        if ($novel_data['type'] == "short") {
            ?>

            <section class="novel-text mb-6 p-4 border border-gray-200 rounded shadow-md flex flex-col items-center">
                <h2 class="section-title text-2xl font-bold text-center">本文</h2>
                <p class="text-center"><?php echo all_convert($novel_data['text']); ?></p>
            </section>

            <?php
        } else {
            if (isset($_GET['episode'])) {
                $episode_id = $_GET["episode"];
                $episode_data = $Novel->fetch_episode($episode_id);

                if ($novel_data) {
                    $Novel->add_watch($user_data['id'], $novel_id);
                }

                $episode_title = $episode_data['title'];
                $episode_text = $episode_data['text'];
                $episode_update_date = $episode_data['update_date'];

                ?>

                <section class="novel-text mb-6 p-4 border border-gray-200 rounded shadow-md flex flex-col items-center">
                    <h2 class="episode-title text-2xl font-bold text-center"><?php echo $episode_title; ?></h2>
                    <p class="text-center"><?php echo all_convert($episode_text); ?></p>
                </section>

                <?php
            } else {
                echo '<section class="novel-episodes mb-6">';
                echo '<h2 class="section-title text-2xl font-bold text-center">公開されてるエピソード</h2>';
                foreach ($novel_episodes as $episode_id) {
                    $episode_data = $Novel->fetch_episode($episode_id);
                    $episode_title = $episode_data['title'];
                    $episode_update_date = $episode_data['update_date'];
                    ?>
                    <a href="./?novel=<?php echo $novel_id; ?>&episode=<?php echo $episode_id; ?>" class="block mt-2 text-blue-500 hover:underline"><?php echo $episode_title; ?> <span class="text-gray-500">Update: <?php echo $episode_update_date; ?></span></a>

                    <?php
                }
                echo '</section>';
            }
        }
        ?>

        <?php if ($user_data): ?>

            <section class="follow-novel mb-6">
                <a href="/api/follow?type=novel&novel=<?php echo $novel_id; ?>&redirect_url=<?php echo getCurrentURL(); ?>" class="link-button bg-blue-500 text-white p-2 rounded hover:bg-blue-600">
                    <?php echo $Novel->isFollow($user_data['id'], $novel_id) ? 'フォロー解除' : 'フォロー'; ?>
                </a>
            </section>

        <?php else: ?>

            <section class="follow-novel mb-6">
                <a href="/login" class="link-button text-blue-500 hover:underline">ログインしてフォロー機能を解除しよう</a>
            </section>

        <?php endif; ?>

        <?php
        echo '</main>';
    }
}
