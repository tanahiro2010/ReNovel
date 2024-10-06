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
    <section class="error mb-4 p-4 border border-red-500 bg-red-100 text-red-700 rounded">
        <h2 class="error-title font-bold"><?php echo $error_title; ?></h2>
        <div class="error-description"><?php echo $error_content; ?></div>
        <button class="error-close mt-2 text-red-600 hover:text-red-800" onclick="this.parentElement.style.display='none';">×</button>
    </section>
<?php endif; ?>

<section class="register-box p-4 bg-white border rounded shadow-md">
    <form action="./" method="post" class="register-form">
        <h2 class="section-title text-2xl font-bold mb-4">登録</h2>

        <label class="label-register block mb-2">
            Name:
            <input type="text" name="name" placeholder="Name" required class="mt-1 border border-gray-300 p-2 rounded w-full" />
        </label>

        <label class="label-register block mb-2">
            ID:
            <input type="text" name="id" placeholder="ID" required class="mt-1 border border-gray-300 p-2 rounded w-full" />
        </label>

        <label class="label-register block mb-2">
            Mail:
            <input type="email" name="mail" placeholder="Mail" required class="mt-1 border border-gray-300 p-2 rounded w-full" />
        </label>

        <label class="label-register block mb-2">
            Password:
            <input type="password" name="password" placeholder="Password" required class="mt-1 border border-gray-300 p-2 rounded w-full" />
        </label>

        <button type="submit" class="submit bg-blue-500 text-white p-2 rounded hover:bg-blue-600">登録</button>

        <div class="mt-4">
            <a href="/login" class="big-text text-blue-500 hover:underline">ログインしますか？</a>
        </div>
    </form>
</section>
