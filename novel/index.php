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

        echo '<main>';

        $novel_title = $novel_data['title'];
        $novel_description = $novel_data['description'];
        $novel_update_date = $novel_data['last_update'];
        $novel_episodes = $novel_data['episodes'] ?? null;

        ?>

        <section class="novel-content-show"  onclick="location.href = './?novel=<?php echo $novel_id; ?>';">
            <h2 class="section-title"><?php echo $novel_title; ?></h2>
            作者: <a href="/user?user=<?php echo $novel_data['author_id']; ?>" class="link-button"><?php echo $Account->fetch($novel_data['author_id'], 'name'); ?></a>
            <div class="section-description">
                <h3>紹介文</h3>
                <?php echo all_convert($novel_description); ?>
            </div>
        </section>

        <?php

        if ($novel_data['type'] == "short") {
            ?>

            <section class="novel-text">
                <h2 class="section-title">本文</h2>
                <?php echo all_convert($novel_data['text']); ?>
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

                <section class="novel-text">
                    <h2 class="episode-title"><?php echo $episode_title; ?></h2>
                    <?php echo all_convert($episode_text); ?>
                </section>

                <?php
            } else {
                echo '<section class="novel-episodes">';
                echo '<h2 class="section-title">公開されてるエピソード</h2>';
                foreach ($novel_episodes as $episode_id) {
                    $episode_data = $Novel->fetch_episode($episode_id);
                    $episode_title = $episode_data['title'];
                    $episode_text = $episode_data['text'];
                    $episode_update_date = $episode_data['update_date'];
                    ?>
                        <a href="./?novel=<?php echo $novel_id; ?>&episode=<?php echo $episode_id; ?>"><?php echo $episode_title; ?> Update: <?php echo $episode_update_date; ?></a>

                <?php
                }
                echo '</section>';
            }
        }
        ?>

        <?php if ($user_data): ?>

            <section class="follow-novel">
                <a href="/api/follow?type=novel&novel=<?php echo $novel_id; ?>&redirect_url=<?php echo getCurrentURL(); ?>" class="link-button">
                    <?php echo $Novel->isFollow($user_data['id'], $novel_id) ? 'フォロー解除' : 'フォロー'; ?>
                </a>
            </section>

        <?php else: ?>

            <section class="follow-novel">
                <a href="/login" class="link-button">ログインしてフォロー機能を解除しよう</a>
            </section>

        <?php endif; ?>

        <?php
        echo '</main>';
    }
}