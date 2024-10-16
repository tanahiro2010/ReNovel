<?php
require_once '../functions/app.php';
require_once '../functions/header.php';

$Account = new DataBase('../db/database.json', 'novel');
$Novel = new Novel('../db/database.json', 'novel');
$user_data = $Account->isLogin();

$location_url = getCurrentURL(); // 現在のサイトのURL

if (!$user_data) { // ログインしていなかったら
    header('Location: /login');
    exit();
}

echo_header($user_data);
?>

<main class="container mx-auto px-4 py-8">
    <!-- ユーザーデータセクション -->
    <section class="user-data bg-white shadow-md rounded-lg p-6 mb-8">
        <h2 class="text-2xl font-bold mb-4"><?php echo $user_data['name']; ?>さん、ようこそ！！</h2>
        <div class="text-gray-700">
            ここからアカウントや小説の操作をしてください
        </div>
    </section>

    <!-- 小説セクション -->
    <section class="my-novel bg-white shadow-md rounded-lg p-6 mb-8">
        <h2 class="text-xl font-bold mb-4">小説</h2>
        <div class="space-y-4">
            <a href="./novel/create" class="block text-center bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                新規小説を作成
            </a>
            <a href="./novel/edit" class="block text-center bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                小説を編集
            </a>
        </div>
    </section>

    <!-- フォローしている小説セクション -->
    <section class="follow-novel bg-white shadow-md rounded-lg p-6 mb-8">
        <h2 class="text-xl font-bold mb-4">フォローしている小説</h2>
        <div class="space-y-4">
            <?php
            $following_novels = $user_data['follow-novel'];
            foreach ($following_novels as $novel_id):
                $novel_data = $Novel->fetch_novel($novel_id);
                ?>
                <div class="flex justify-between items-center bg-gray-100 p-4 rounded-lg">
                    <div class="text-gray-800 font-medium cursor-pointer" onclick="location.href = '/novel?novel=<?php echo $novel_id; ?>';">
                        <?php echo $novel_data['title']; ?>
                    </div>
                    <a href="/api/follow?type=novel&novel=<?php echo $novel_id; ?>&redirect_url=<?php echo $location_url; ?>"
                       class="text-red-500 hover:underline">
                        フォロー解除
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- フォロー & フォロワーセクション -->
    <section class="follow-controller bg-white shadow-md rounded-lg p-6 mb-8">
        <h2 class="text-xl font-bold mb-4">フォロー & フォロワー</h2>

        <!-- フォローしているユーザー -->
        <h3 class="text-lg font-semibold mb-2">フォローしているユーザー</h3>
        <div class="space-y-4">
            <?php
            $following = $user_data['following'];
            foreach ($following as $following_user_id):
                $following_user_data = $Account->in_account($following_user_id);
                ?>
                <div class="flex justify-between items-center bg-gray-100 p-4 rounded-lg">
                    <div class="text-gray-800 font-medium cursor-pointer" onclick="location.href = '/@<?php echo $following_user_id; ?>'">
                        <?php echo $following_user_data['name']; ?>
                    </div>
                    <a href="/api/follow?type=user&user=<?php echo $following_user_id; ?>&redirect_url=<?php echo $location_url; ?>"
                       class="text-red-500 hover:underline">
                        フォロー解除
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- フォロワー -->
        <h3 class="text-lg font-semibold mt-6 mb-2">現在自分をフォローしているユーザー</h3>
        <div class="space-y-4">
            <?php
            $followers = $user_data['followers'];
            foreach ($followers as $follower_user_id):
                $follower_user_data = $Account->in_account($follower_user_id);
                ?>
                <div class="flex justify-between items-center bg-gray-100 p-4 rounded-lg">
                    <div class="text-gray-800 font-medium cursor-pointer" onclick="location.href = '/@<?php echo $follower_user_id; ?>'">
                        <?php echo $follower_user_data['name']; ?>
                    </div>
                    <a href="/api/follow?type=user&user=<?php echo $follower_user_id; ?>&redirect_url=<?php echo $location_url; ?>"
                       class="text-<?php echo in_array($follower_user_id, $following) ? 'red' : 'blue'; ?>-500 hover:underline">
                        <?php echo in_array($follower_user_id, $following) ? "フォロー解除" : "フォロー" ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- アカウント制御セクション -->
    <section class="account-control bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-bold mb-4">アカウント</h2>
        <a href="/logout" class="block text-center bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">
            ログアウト
        </a>
    </section>
</main>