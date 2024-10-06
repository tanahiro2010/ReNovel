<?php
require_once '../functions/app.php';
require_once '../functions/header.php';

$Account = new DataBase('../db/database.json', 'novel');
$Novel = new Novel('../db/database.json', 'novel');

$query = "";

$user_data = $Account->isLogin();

$novels = $Novel->get_novels();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['q'])) {
        $query = all_convert($_GET['q']);

        foreach ($novels as $novel_id => $novel_data) {
            if (!$query == $novel_id && !strpos($novel_data['title'], $_GET['q']) && !strpos($novel_data['description'], $_GET['q'])) {
                unset($novels[$novel_id]);
            }
        }

        $query = ': ' . $query;
    }

    echo_header($user_data);
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    exit();
}

?>

<main>
    <section class="novel-search-form">
        <h2 class="section-title">検索<?php echo $query; ?></h2>
        <form action="./" method="get" class="search-form">
            <label class="create-novel-label">
                検索:
                <input type="text" name="q" placeholder="検索" value="<?php echo $_GET['q'] ?? ''; ?>" required>
            </label>

            <button type="submit" class="submit">検索</button>
        </form>
    </section>

    <section class="novels">
        <h2 class="section-title">
            <?php if (isset($_GET['q'])): ?>
                検索結果
            <?php else: ?>
                投稿されている小説
            <?php endif; ?>
        </h2>

        <?php foreach ($novels as $novel_id => $novel_data): ?>

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
</main>
