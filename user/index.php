<?php
require_once '../functions/app.php';
require_once '../functions/header.php';

$Account = new DataBase('../db/database.json', 'novel');
$Novel = new Novel('../db/database.json', 'novel');
$user_data = $Account->isLogin();

$target_name = "";
$target_id = "";
$target_data = [];
$target_novels = [];

$location_url = getCurrentURL();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user'])) {
    $target_id = all_convert($_GET['user']);
    $target_data = $Account->in_account($target_id);

    if ($target_data) {
        $target_name = all_convert($target_data['name']);
        $target_novels = $target_data['my_novel'];
    } else {
        header('HTTP/1.0 404 Not Found');
        header('Location: /');
        exit();
    }
} else {
    header('HTTP/1.1 400 Bad Request');
    header('Location: /');
    exit();
}

echo_header($user_data);
?>

<main class="container mx-auto p-6">
    <section class="bg-white rounded-lg shadow-lg p-4 mb-6">
        <h2 class="text-2xl font-bold"><?php echo $target_name; ?></h2>
        <div class="text-gray-600">@<?php echo $target_id; ?></div>

        <?php if ($user_data): ?>
            <br>
            <a href="/api/follow?type=user&user=<?php echo $target_id; ?>&redirect_url=<?php echo $location_url; ?>" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                <?php echo in_array($target_id, $Account->fetch($user_data['id'], 'following')) ? 'フォロー解除' : 'フォロー'; ?>
            </a>
        <?php else: ?>
            <br>
            <a href="/login" class="bg-gray-400 text-white py-2 px-4 rounded hover:bg-gray-500">ログインしてフォロー機能を解除しよう!!</a>
        <?php endif; ?>
    </section>

    <?php if (!empty($target_novels)): ?>
        <section class="bg-white rounded-lg shadow-lg p-4 mb-6">
            <h2 class="text-xl font-bold">投稿している小説</h2>
            <?php foreach ($target_novels as $novel_id):
                $novel_data = $Novel->fetch_novel($novel_id);
                if ($novel_data['status'] === 'public'): ?>
                    <div class="show-novel-block bg-gray-100 p-4 mb-4 rounded-lg cursor-pointer hover:bg-gray-200" onclick="location.href = '/novel?novel=<?php echo $novel_id; ?>';">
                        <div class="novel-title text-lg font-semibold"><?php echo $novel_data['title']; ?></div>
                        <div class="novel-description text-gray-600"><?php echo substr(all_convert($novel_data['description']), 0, 20); ?>...</div>
                        <div class="novel-watch text-gray-500"><?php echo count($novel_data['watch']); ?>回講読</div>
                        <div class="novel-point text-gray-500"><?php echo count($novel_data['followers']); ?>ポイント</div>
                        <div class="novel-update-date text-gray-500"><?php echo $novel_data['last_update']; ?></div>
                    </div>
                <?php endif;
            endforeach; ?>
        </section>
    <?php endif; ?>

    <section class="bg-white rounded-lg shadow-lg p-4 mb-6">
        <h2 class="text-xl font-bold">お気に入りの小説</h2>
        <?php foreach ($target_data['follow-novel'] as $novel_id):
            $novel_data = $Novel->fetch_novel($novel_id); ?>
            <div class="show-novel-block bg-gray-100 p-4 mb-4 rounded-lg cursor-pointer hover:bg-gray-200" onclick="location.href = '/novel?novel=<?php echo $novel_id; ?>';">
                <div class="novel-title text-lg font-semibold"><?php echo all_convert($novel_data['title']); ?></div>
                <div class="novel-description text-gray-600"><?php echo all_convert(substr($novel_data['description'], 0, 20)); ?>...</div>
                <div class="novel-watch text-gray-500"><?php echo count($novel_data['watch']); ?>回講読</div>
                <div class="novel-point text-gray-500"><?php echo count($novel_data['followers']); ?>ポイント</div>
                <div class="novel-update-date text-gray-500"><?php echo all_convert($novel_data['last_update']); ?></div>
            </div>
        <?php endforeach; ?>
    </section>

    <section class="bg-white rounded-lg shadow-lg p-4 mb-6">
        <h2 class="text-xl font-bold">フォローしているユーザー</h2>
        <div class="list-follow">
            <?php foreach ($target_data['following'] as $following_user_id):
                $following_user_data = $Account->in_account($following_user_id); ?>
                <div class="following-user flex justify-between items-center py-2 border-b">
                    <div class="following-user-name text-gray-700"><?php echo all_convert($following_user_data['name']); ?></div>
                    <a href="/@<?php echo all_convert($following_user_id); ?>" class="text-blue-500 hover:underline">ユーザーページへ行く</a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>
