<?php
require_once './functions/app.php';
require_once './functions/header.php';
$Account = new DataBase('./db/database.json', 'novel');
$Novel = new Novel('./db/database.json', 'novel');

$novels = $Novel->get_novels();

$userData = $Account->isLogin();
echo_header($userData);
?>

<main class="p-4">
    <section class="novels grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($novels as $novel_id => $novel_data) { ?>
            <?php if ($novel_data['status'] == 'public'): ?>
                <div class="show-novel-block p-4 border border-gray-200 rounded shadow-md cursor-pointer hover:shadow-lg transition-shadow" onclick="location.href = '/novel?novel=<?php echo $novel_id; ?>';">
                    <div class="novel-title text-xl font-bold mb-2">
                        <?php echo $novel_data['title']; ?>
                    </div>

                    <div class="novel-description text-gray-600 mb-2">
                        <?php echo substr($novel_data['description'], 0, 20); ?>...
                    </div>

                    <div class="novel-watch text-gray-500 mb-1">
                        <?php echo count($novel_data['watch']); ?> 回講読
                    </div>

                    <div class="novel-point text-gray-500 mb-1">
                        <?php echo count($novel_data['followers']); ?> ポイント
                    </div>

                    <div class="novel-update-date text-gray-500">
                        <?php echo $novel_data['last_update']; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php } ?>
    </section>
</main>
