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
            exit();
        } else {
            header('Location: ./?error=account');
            exit();
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
    <br><br>
    <!-- error mb-4 p-4 border border-red-500 bg-red-100 text-red-700 rounded -->
    <section class=" p-6 max-w-md mx-auto bg-red-100 border-white-500 shadow-md rounded"> <!-- ここ背景赤色にしたい -->
        <h2 class="error-title font-bold"><?php echo $error_title; ?></h2>
        <div class="error-description"><?php echo $error_content; ?></div>
        <button class="error-close mt-2 text-red-600 hover:text-red-800" onclick="this.parentElement.style.display='none';">×</button>
    </section>
<?php endif; ?>

<br>
<br>

<section class="login-box p-6 max-w-md mx-auto bg-white shadow-md rounded">
    <form action="./" method="post" class="login-form">
        <h2 class="section-title text-2xl font-bold mb-4">ログイン</h2>
        <label class="label-login block mb-2">
            ID:
            <input type="text" name="id" placeholder="ID" required class="border rounded p-2 w-full">
        </label>

        <label class="label-login block mb-4">
            Password:
            <input type="password" name="password" placeholder="Password" required class="border rounded p-2 w-full">
        </label>

        <button type="submit" class="submit bg-blue-500 text-white py-2 px-4 rounded">ログイン</button>

        <div class="mt-4">
            <a href="/register" class="text-blue-600 hover:underline">登録しますか？</a><br>
            <a href="./forget_password" class="text-sm text-blue-600 hover:underline">パスワードを忘れた場合</a>
        </div>
    </form>
</section>
