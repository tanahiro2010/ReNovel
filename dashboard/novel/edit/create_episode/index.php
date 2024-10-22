<?php
require_once '../../../../functions/app.php';
require_once '../../../../functions/header.php';

$Account = new DataBase('../../../../db/database.json', 'novel');
$Novel = new Novel('../../../../db/database.json', 'novel');

$user_data = $Account->isLogin();

if (!$user_data) {
    header('Location: /login');
    exit();
}

$novel_id = $_SESSION['edit_novel_id'];
$novel_data = $Novel->fetch_novel($novel_id);
if ($novel_data) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['title']) && isset($_POST['text'])) {
            $episode_id = $Novel->uploadEpisode($novel_id, $_POST['title'], $_POST['text']);
            header('Location: ../?novel=' . $novel_id . '&episode=' . $episode_id);
            exit();
        } else {
            header('Location: ./?error=required');
        }
    } elseif ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header('Location: /');
        exit();
    }
} else {
    header('Location: /dashboard');
    exit();
}

echo_header($user_data);
?>

<main class="p-4 text-center">
    <section class="control_novel mb-6">
        <h2 class="section-title text-2xl font-bold mb-2">
            小説: <a href="../?novel=<?php echo $novel_id; ?>" class="text-blue-600 hover:underline"><?php echo $novel_data['title']; ?></a>
        </h2>
        <div class="section-description">
            <?php echo all_convert($novel_data['description']); ?>
        </div>
    </section>

    <section class="create-novel-form mb-6">
        <h2 class="section-title text-xl font-semibold mb-2">新規エピソードを作成</h2>
        <form action="./" method="post" class="create-novel">
            <label class="block mb-2">
                エピソードタイトル
                <input type="text" name="title" placeholder="タイトル" value="第<?php echo count($novel_data['episodes']) + 1; ?>話" required class="border rounded p-2 w-full">
            </label>

            <label for="text" class="block mb-2">本文</label>
            <textarea class="novel-text-form border rounded p-2 w-full h-96" name="text" placeholder="本文" required></textarea>
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">作成</button>
        </form>
    </section>
</main>
