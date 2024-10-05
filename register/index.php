<?php
require_once '../functions/app.php';
require_once '../functions/header.php';

$Account = new DataBase('../db/database.json', 'novel');
$user_data = $Account->isLogin();

if ($user_data) { // もしログインしているなら
    header('Location: /');
    exit();
}

$error = false;
$error_title = "";
$error_content = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['mail']) && isset($_POST['password'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $mail = $_POST['mail'];
        $password = $_POST['password'];

        $result = $Account->register($id, $name, $mail, $password);

        if ($result) {
            header('Location: /');
        } else {
            header('Location: ./?error=id');
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['error'])) {
        $error = true;

        switch ($_GET['error']) {
            case 'id':
                $error_title = "アカウントが存在しています";
                $error_content = "そのアカウントIDはすでに存在しています<br>別のIDを使用してください";
                break;
        }
    }
}

echo_header($user_data);

?>

<?php if ($error): ?>
    <section class="error">
        <h2 class="error-title"><?php echo $error_title; ?></h2>
        <div class="error-description"><?php echo $error_content; ?></div>
        <button class="error-close" onclick="this.parentElement.style.display='none';">×</button>
    </section>
<?php endif; ?>

<section class="register-box">

    <form action="./" method="post" class="register-form">
        <h2 class="section-title">登録</h2>
        <label class="label-register">
            Name:
            <input type="text" name="name" placeholder="Name" required>
        </label><br>

        <label class="label-register">
            ID:
            <input type="text" name="id" placeholder="ID" required>
        </label><br>

        <label class="label-register">
            Mail:
            <input type="email" name="mail" placeholder="Mail" required>
        </label><br>

        <label class="label-register">
            Password:
            <input type="password" name="password" placeholder="Password" required>
        </label><br>

        <button type="submit" class="submit">登録</button><br>

        <a href="/login" class="big-text">ログインしますか？</a><br>
    </form>
</section>
