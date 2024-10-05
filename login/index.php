<?php
require_once '../functions/app.php';
require_once '../functions/header.php';

$Account = new DataBase('../db/database.json', 'novel');

$user_data = $Account->isLogin();

if ($user_data) { // ログインしているなら
    header('Location: /');
    exit();
}

$error = false;
$error_title = "";
$error_content = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id']) && isset($_POST['password'])) {
        $id = $_POST['id'];
        $password = $_POST['password'];

        $result = $Account->login($id, $password);

        if ($result) {
            header('Location: /');
        } else {
            header('Location: ./?error=account');
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['error'])) {
        $error = true;
        switch ($_GET['error']) {
            case 'account':
                $error_title = "アカウントエラー";
                $error_content = "そのようなアカウントは存在しない、またはパスワードが違う可能性があります";
                break;
        }
    }
}

// ログインしていないなら
echo_header($user_data); // ヘッダー出力
?>
<?php if ($error): ?>
    <section class="error">
        <h2 class="error-title"><?php echo $error_title; ?></h2>
        <div class="error-description"><?php echo $error_content; ?></div>
        <button class="error-close" onclick="this.parentElement.style.display='none';">×</button>
    </section>
<?php endif; ?>

<section class="login-box">
    <form action="./" method="post" class="login-form">
        <h2 class="section-title">ログイン</h2>
        <label class="label-login">
            ID:
            <input type="text" name="id" placeholder="ID" required>
        </label><br>

        <label class="label-login">
            Password:
            <input type="password" name="password" placeholder="Password" required>
        </label><br>

        <button type="submit" class="submit">ログイン</button><br>

        <a href="/register" class="big-text">登録しますか？</a><br>
        <a href="./forget_password" class="mini-text">パスワードを忘れた場合</a><br>
    </form>
</section>