<?php
require_once '../functions/app.php';
require_once '../functions/header.php';

$Account = new DataBase('../db/database.json', 'novel');
$Novel = new Novel('../db/database.json', 'novel');
$user_data = $Account->isLogin();

$location_url = getCurrentURL(); // 現在のサイトのURL

if (!$user_data) { // ログインしてなかったら
    header('Location: /login');
    exit();
}

echo_header($user_data);
?>

<main>
    <section class="user-data">
        <h2 class="section-title"><?php echo $user_data['name']; ?>さん、ようこそ！！</h2>
        <div class="section-description">
            ここからアカウントや小説の操作をしてください
        </div>
    </section>

    <section class="my-novel">
        <h2 class="section-title">小説</h2>
        <div class="novel">
            <a href="./novel/create" class="link-button">新規小説を作成</a><br>
            <a href="./novel/edit" class="link-button">小説を編集</a><br>
        </div>
    </section>

    <section class="follow-novel">
        <h2 class="section-title">フォローしている小説</h2>
        <div class="list-follow">
            <?php
            $following_novels = $user_data['follow-novel'];
            foreach ($following_novels as $novel_id):
                $novel_data = $Novel->fetch_novel($novel_id);
                ?>

                <div class="following-user">
                    <div class="following-user-name"><?php echo $novel_data['title']; ?></div>
                    <a href="/api/follow?type=novel&novel=<?php echo $novel_id; ?>&redirect_url=<?php echo $location_url; ?>">
                        フォロー解除
                    </a>
                </div>

            <?php endforeach; ?>
        </div>
    </section>

    <section class="follow-controller">
        <h2 class="section-title">フォロー & フォロワー</h2>

        <h3 class="section-sub-title">フォローしているユーザー</h3>
        <div class="list-follow">
            <?php
            $following = $user_data['following'];
            foreach ($following as $following_user_id):
                $following_user_data = $Account->in_account($following_user_id);
                ?>

                <div class="following-user">
                    <div class="following-user-name" onclick="location.href = '/@<?php echo $following_user_id; ?>'"><?php echo $following_user_data['name']; ?></div>
                    <a href="/api/follow?type=user&user=<?php echo $following_user_id; ?>&redirect_url=<?php echo $location_url; ?>">フォロー解除</a>
                </div>

            <?php endforeach; ?>
        </div>

        <h3 class="section-sub-title">現在自分をフォローしているユーザー</h3>
        <div class="list-follow">
            <?php
            $followers = $user_data['followers'];
            foreach ($followers as $follower_user_id):
                $follower_user_data = $Account->in_account($follower_user_id);
                ?>

                <div class="following-user">
                    <div class="following-user-name"><?php echo $follower_user_data['name']; ?></div>
                    <a href="/api/follow?type=user&user=<?php echo $follower_user_id; ?>&redirect_url=<?php echo $location_url; ?>">
                        <?php echo in_array($follower_user_id, $following) ? "フォロー解除" : "フォロー" ?>
                    </a>
                </div>

            <?php endforeach; ?>
        </div>
    </section>

    <section class="account-control">
        <h2 class="section-title">アカウント</h2>
        <a href="/logout" class="link-button">ログアウト</a>
    </section>
</main>
