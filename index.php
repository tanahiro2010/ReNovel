<?php
require_once './functions/app.php';
require_once './functions/header.php';
$Account = new DataBase('./db/database.json', 'novel');
$Novel = new Novel('./db/database.json', 'novel');

$novels = $Novel->get_novels();

$userData = $Account->isLogin();
echo_header($userData);
?>

<main>
    <section class="novels">
        <?php foreach ($novels as $novel_id => $novel_data) { ?>
            <?php if ($novel_data['status'] == 'public'): ?>
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
            <?php endif; ?>
        <?php } ?>
    </section>
</main>
