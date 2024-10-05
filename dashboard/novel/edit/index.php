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
    if (isset($_POST['type'])) {
        switch ($_POST['type']) {
            case 'edit_novel':
                if (isset($_POST['title']) && isset($_POST['status'])) {
                    $Novel->editNovel($_SESSION['edit_novel_id'], $_POST['title'], $_POST['description'] ?? "", $_POST['status'], $_POST['text'] ?? null);
                    header('location: ./?novel=' . $_SESSION['edit_novel_id']);
                }
                break;
            case 'delete_novel':

                break;

            case 'edit_episode':
                if (isset($_POST['title']) && isset($_POST['status']) && isset($_POST['text'])) {
                    $episode_id = $_SESSION['edit_episode_id'];
                    $Novel->editEpisode(
                        $episode_id,
                        $_POST['title'],
                        $_POST['text'],
                    );

                    if ($_POST['status'] == "private") {
                        $Novel->toPrivate_episode($episode_id);
                    } else if ($_POST['status'] == "public") {
                        $Novel->toPublic_episode($episode_id);
                    }

                    header('Location: ./?novel=' . $_SESSION['edit_novel_id'] . '&episode=' . $episode_id);
                    exit();
                }
                break;
        }
    }

    header('location: ./');
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    echo_header($user_data);
    echo '<main>';
    if (isset($_GET['novel'])) {
        $novel_id = $_GET['novel'];
        $novel_data = $Novel->fetch_novel($novel_id);
        if ($novel_data) {
            $_SESSION['edit_novel_id'] = $novel_id;
        } else {
            header('location: ./');
            exit();
        }

        ?>
        <section class="control_novel">
            <h2 class="section-title">小説: <?php echo $novel_data['title']; ?></h2>
            <div class="section-description">
                <?php echo all_convert($novel_data['description']); ?>
            </div>
        </section>
        <?php
        if (isset($_GET['episode'])) { // エピソード編集モード
            $episode_id = $_GET['episode'];
            $episode_data = $Novel->fetch_episode($episode_id);
            if ($episode_data) {
                $_SESSION['edit_episode_id'] = $episode_id;
            } else {
                header('location: ./?novel=' . $novel_id);
                exit();
            }


            ?>

            <section class="create-novel-form">
                <form action="./" method="post" class="create-novel">
                    <h2 class="section-title">エピソード: <?php echo $episode_data['title']; ?> 編集</h2>
                    <input type="hidden" name="type" value="edit_episode" />
                    <label class="create-novel-label">
                        エピソードタイトル:
                        <input type="text" name="title" placeholder="タイトル" value="<?php echo $episode_data['title']; ?>" required />
                    </label><br>

                    <label class="create-novel-label">
                        ステータス:
                        <select name="status" required>
                            <option value="public" <?php echo $episode_data['status'] == "public" ? "selected" : "" ?>>公開</option>
                            <option value="private" <?php echo $episode_data['status'] == "private" ? "selected" : "" ?>>非公開</option>
                        </select>
                    </label><br>

                    <label class="create-novel-label" for="text">本文</label>
                    <textarea class="novel-text-form" name="text" placeholder="本文" required><?php echo $episode_data['text']; ?></textarea><br>

                    <button type="submit" class="submit">更新</button>
                </form>
                <br>
                <a href="/api/delete?type=episode&episode=<?php echo $episode_id; ?>&redirect_url=/dashboard/novel/edit?novel=<?php echo $novel_id; ?>" class="link-button">エピソードを削除</a>
            </section>
            <?php
        } else {                       // エピソード選択モード ?>
            <section class="edit-novel"> <!-- 小説の詳細を編集 -->
                <h2 class="section-title">小説詳細編集</h2>
                <form class="edit-novel-content create-novel" action="./" method="post">
                    <input type="hidden" name="type" value="edit_novel">

                    <label class="create-novel-label">
                        タイトル:
                        <input type="text" name="title" value="<?php echo $novel_data['title']; ?>" required>
                    </label><br>

                    <label for="description">紹介文</label><br>
                    <textarea name="description" class="novel-description"><?php echo $novel_data['description']; ?></textarea><br>

                    <label class="create-novel-label">
                        ステータス:
                        <select name="status">
                            <option value="public" <?php echo $novel_data['status'] == "public" ? "selected" : "" ?>>公開</option>
                            <option value="private" <?php echo $novel_data['status'] == "private" ? "selected" : "" ?>>非公開</option>
                        </select>
                    </label><br>


                    <?php if ($novel_data['type'] == 'short'): ?>

                    <label for="text" class="create-novel-label">本文</label>
                    <textarea name="text" placeholder="本文" class="novel-text-form" required><?php echo $novel_data['text']; ?></textarea>

                    <?php endif; ?>

                    <button type="submit" class="submit">更新</button><br>
                </form>
            </section>

            <?php if ($novel_data['type'] == 'long'): ?>

                <section class="select-episode">
                    <h2>エピソードを選択</h2>
                    <?php foreach ($novel_data['episodes'] as $episode_id):
                    $episode_data = $Novel->fetch_episode($episode_id) ?? array(
                        "title" => "読み込みエラー",
                        "status" => "Error",
                        "update_date" => "読み込みエラー"
                    );
                    ?>

                    <div class="select-episode" onclick="location.href='./?novel=<?php echo $novel_id; ?>&episode=<?php echo $episode_id; ?>'">
                        <div class="episode-title"><?php echo $episode_data['title']; ?></div>
                        <div class="status">ステータス: <?php echo $episode_data['status'] == "public" ? "公開" : "非公開"; ?></div>
                        <div class="last-update"><?php echo $episode_data['update_date']; ?></div>
                    </div>

                    <?php endforeach; ?>

                    <section class="create-episode">
                        <br>
                        <a href="./create_episode" class="link-button">エピソードを新規作成</a>
                    </section>
                </section>

                <a href="/api/delete?type=novel&novel=<?php echo $novel_id; ?>&redirect_url=/dashboard/novel/edit" class="link-button">小説を削除</a>
            <?php endif;
        }

    } else { // 小説選択モード
        ?>
        <section class="select-novel">
            <h2 class="section-title">小説を選択</h2>
            <div class="list-novels">
                <?php
                foreach ($user_data['my_novel'] as $novel_id):
                    $novel_data = $Novel->fetch_novel($novel_id);
                    ?>
                    <div class="novel-select" onclick="location.href = './?novel=<?php echo $novel_id; ?>'">
                        <h3 class="novel-title-mini">
                            <?php echo $novel_data['title']; ?>
                        </h3>
                        <div class="novel-description-mini">
                            <div class="last-update-day"><?php echo $novel_data['last_update']; ?></div>
                            <div class="novel-description-text">
                                <?php echo substr($novel_data['description'], 0, 100); ?>
                            </div>
                            <div class="status-label">
                                ステータス: <?php echo $novel_data['status']; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php
    }
    echo '</main>';
} else {
    header('HTTP/1.0 405 Method Not Allowed');
}
?>