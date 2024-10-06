<?php
require_once '../../../functions/app.php';
require_once '../../../functions/header.php';

$Account = new DataBase('../../../db/database.json', 'novel');
$Novel = new Novel('../../../db/database.json', 'novel');

$user_data = $Account->isLogin();

if (!$user_data) { // ログインしていないなら
    header('location: /login');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $paramsAllInput = (
        isset($_POST['title']) &&
        isset($_POST['description']) &&
        isset($_POST['genre']) &&
        isset($_POST['tags']) &&
        isset($_POST['type'])
    );

    if ($paramsAllInput) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $genre = $_POST['genre'];
        $tags = $_POST['tags'];
        $type = $_POST['type'];
        $text = $_POST['text'] ?? "";
        try {
            $novel_id = $Novel->createNovel($user_data['id'], $title, $description, $genre, $tags, $type, $text);

            header('location: ../edit?novel=' . $novel_id);
        } catch (PDOException $e) {
            header('Location: ./error=' . $e->getMessage());
        }

    } else {
        header('location: ./');
    }
} elseif (!$_SERVER['REQUEST_METHOD'] == 'GET') {
    header('HTTP/1.1 405 Method Not Allowed');
}

echo_header($user_data);
if ($_SERVER['REQUEST_METHOD'] == 'GET'): ?>

    <main class="p-6">
        <?php if (isset($_GET['error'])): // エラー表示 ?>
            <section class="bg-red-100 border border-red-400 text-red-700 p-4 rounded mb-4">
                <h2 class="font-bold text-lg">エラーが発生しました</h2>
                <div class="mt-2">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="bg-white shadow-md rounded p-6">
            <h2 class="text-2xl font-semibold mb-4">小説を作成</h2>

            <form action="./" method="post" class="space-y-4">
                <label class="block">
                    <span class="text-gray-700">小説タイトル:</span>
                    <input type="text" name="title" placeholder="タイトル" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                </label>

                <label class="block">
                    <span class="text-gray-700">小説タイプ:</span>
                    <select name="type" id="type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="long">連載</option>
                        <option value="short">短編</option>
                    </select>
                </label>

                <label for="description" class="block">
                    <span class="text-gray-700">紹介文:</span>
                    <textarea name="description" placeholder="紹介文" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                </label>

                <label class="block">
                    <span class="text-gray-700">ジャンル:</span>
                    <select name="genre" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        <option value="other-world-fantasy-tensei">異世界ファンタジー (転生)</option>
                        <option value="other-world-fantasy-metastatic">異世界ファンタジー (転移)</option>
                        <option value="this-world-fantasy">現代ファンタジー</option>
                        <option value="other-world-fantasy-love">異世界ファンタジー (恋愛)</option>
                        <option value="this-world-love">現代恋愛</option>
                    </select>
                </label>

                <label class="block">
                    <span class="text-gray-700">タグ:</span>
                    <input type="text" name="tags" placeholder="タグを入力 半角空白で分割" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </label>

                <div id="short-text" class="hidden">
                    <label class="block">
                        <span class="text-gray-700">本文:</span>
                        <textarea name='text' placeholder='短編小説本文' class='mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50' id="textarea"></textarea>
                    </label>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white font-semibold py-2 rounded hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    作成
                </button>
            </form>
        </section>

        <script src="./js/index.js"></script>
    </main>

<?php endif; ?>
