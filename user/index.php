<?php
require_once '../functions/app.php';
require_once '../functions/header.php';

$Account = new DataBase('../db/database.json', 'novel');
$Novel = new Novel('../db/database.json', 'novel');
$user_data = $Account->isLogin();

$target_name = "";
$target_id = "";
$target_data = array();
$target_novels = array();

$location_url = getCurrentURL();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['user'])) {
        $target_id = $_GET['user'];
        $target_data = $Account->in_account($target_id);

        if ($target_data) { // ユーザーが存在するなら
            $target_name = $target_data['name'];
            $target_novels = $target_data['my_novel'];
        } else {             // ユーザーが存在しないなら
            header('HTTP/1.0 404 Not Found');
            header('Location: /');
            exit();
        }
    } else {
        header('HTTP/1.0 503 Service Unavailable');
        header('Location: /');
        exit();
    }
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    exit();
}

echo_header($user_data);

?>

<main>
    <section class="show-user-content">
        <h2 class="section-title"><?php echo $target_name; ?></h2>
        <div class="display_user_id">@<?php echo $target_id; ?></div>
        <?php if ($user_data): ?>
            <br>
            <a href="/api/follow?type=user&user=<?php echo $target_id; ?>&redirect_url=<?php echo $location_url; ?>" class="link-button">
                <?php if (in_array($target_id, $Account->fetch($user_data['id'], 'following'))): ?>
                    フォロー解除
                <?php else: ?>
                    フォロー
                <?php endif; ?>
            </a>
        <?php else: ?>
            <br>
            <a href="/login" class="link-button">ログインしてフォロー機能を解除しよう!!</a>
        <?php endif; ?>
    </section>

    <?php if (count($target_novels) > 0): ?>
    <section class="novels">
        <h2 class="section-title">投稿している小説</h2>
        <?php foreach ($target_novels as $novel_id):
            $novel_data = $Novel->fetch_novel($novel_id);
            if ($novel_data['status'] == 'public'): ?>
            ?>
            <div class="show-novel-block" onclick="location.href = '/novel?novel=<?php echo $novel_id; ?>';">
                <div class="novel-title">
                    <?php echo $novel_data['title']; ?>
                </div>

                <div class="novel-description">
                    <?php echo substr($novel_data['description'], 0, 20); ?>
                </div>

                <div class="novel-watch">
                    <?php echo count($novel_data['watch']); ?>回講読
                </div>

                <div class="novel-point">
                    <?php echo count($novel_data['followers']); ?>ポイント
                </div>

                <div class="novel-update-date">
                    <?php echo $novel_data['last_update']; ?>
                </div>
            </div>
        <?php endif;
        endforeach; ?>
    </section>
    <?php endif; ?>

    <section class="novels">
        <h2 class="section-title">お気に入りの小説</h2>
        <?php foreach ($target_data['follow-novel'] as $novel_id):
            $novel_data = $Novel->fetch_novel($novel_id);
            ?>
            <div class="show-novel-block" onclick="location.href = '/novel?novel=<?php echo $novel_id; ?>';">
                <div class="novel-title">
                    <?php echo $novel_data['title']; ?>
                </div>

                <div class="novel-description">
                    <?php echo substr($novel_data['description'], 0, 20); ?>
                </div>

                <div class="novel-watch">
                    <?php echo count($novel_data['watch']); ?>回講読
                </div>

                <div class="novel-point">
                    <?php echo count($novel_data['followers']); ?>ポイント
                </div>

                <div class="novel-update-date">
                    <?php echo $novel_data['last_update']; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </section>

    <section class="follow-controller">
        <h2 class="section-title">フォローしているユーザー</h2>
        <div class="list-follow">
            <?php
            $following = $target_data['following'];
            foreach ($following as $following_user_id):
                $following_user_data = $Account->in_account($following_user_id);
                ?>

                <div class="following-user">
                    <div class="following-user-name"><?php echo $following_user_data['name']; ?></div>
                    <a href="/@<?php echo $following_user_id; ?>">ユーザーページへ行く</a>
                </div>

            <?php endforeach; ?>
        </div>
    </section>
</main>
