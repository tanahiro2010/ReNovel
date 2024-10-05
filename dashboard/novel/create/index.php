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

<main>
    <?php if (isset($_GET['error'])): // エラー表示?>
    <section class="error">
        <h2 class="error-title">エラーが発生しました</h2>
        <div class="error-description">
            <?php echo $_GET['error']; ?>
        </div>
    </section>
    <?php endif; ?>

    <section class="create-novel-form">
        <h2 class="section-title">小説を作成</h2>

        <form action="./" method="post" class="create-novel">
            <label class="create-novel-label">
                小説タイトル:
                <input type="text" name="title" placeholder="タイトル" required />
            </label><br>

            <label class="create-novel-label">
                小説タイプ:
                <select name="type" id="type" required>
                    <option value="long">連載</option>
                    <option value="short">短編</option>
                </select>
            </label><br>


            <label for="description" class="create-novel-label">紹介文</label>
            <textarea name="description" placeholder="紹介文" class="novel-description"></textarea><br>

            <label class="create-novel-label">
                ジャンル:
                <select name="genre" class="select-genre" required>
                    <!-- ファンタジー系 -->
                    <option value="other-world-fantasy-tensei">異世界ファンタジー (転生)</option>
                    <option value="other-world-fantasy-metastatic">異世界ファンタジー (転移)</option>
                    <option value="this-world-fantasy">現代ファンタジー</option>

                    <!-- 恋愛系 -->
                    <option value="other-world-fantasy-love">異世界ファンタジー (恋愛)</option>
                    <option value="this-world-love">現代恋愛</option>
                </select>
            </label><br>

            <label class="create-novel-label">
                タグ:
                <input type="text" name="tags" placeholder="タグを入力 半角空白で分割">
            </label><br>

            <div id="short-text" style="display: none">
                <label class='create-novel-label' for="text">本文</label>
                <textarea name='text' placeholder='短編小説本文' class='novel-text-form' id="textarea"></textarea>
            </div>

            <button type="submit" class="submit">作成</button>
        </form>
    </section>

    <script src="./js/index.js"></script>
</main>

<?php endif; ?>