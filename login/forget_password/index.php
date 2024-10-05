<?php
require_once '../../functions/app.php';
require_once '../../functions/header.php';
$Account = new DataBase('../../db/database.json', 'novel');

$user_data = $Account->isLogin();

if ($user_data) {
    header('Location: /');
}

$mode = "mail";

$error = false;
$error_title = "";
$error_content = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['token'])) {
        $token = $_GET['token'];

        if ($Account->forget_password_in_token($token)) {
            $mode = "password";
            $_SESSION['forget_password_token'] = $token;
        }
    }

    if (isset($_GET['error'])) {
        $error = true;
        switch ($_GET['error']) {
            case 'account':
                $error_title = "アカウントが存在しません";
                $error_content = "そのIDのアカウントは存在しません";
                break;

            case 'id':
                $error_title = "入力不足";
                $error_content = "IDを入力してください";
                break;

            case 'token':
                $error_title = "トークンエラー";
                $error_content = "トークンが存在しません";

                break;
        }
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['type'])) {
        $type = $_POST['type'];

        switch ($type) {
            case 'mail':
                if (isset($_POST['id'])) {
                    $user_id = $_POST['id'];
                    if ($Account->in_account($user_id)) {
                        $user_mail = $Account->fetch($user_id, 'mail');
                        $user_name = $Account->fetch($user_id, 'name');

                        $token = $Account->forget_password_create_token($user_id);

                        $mail_content = "$user_name さん、こんにちは。\n下のリンクから、新しいReNovelのパスワードを変更してください\nURL: https://syosetu.tanahiro2010.com/login/forget_password?token=$token";

                        mb_internal_encoding('UTF-8');
                        mb_language('ja');

                        mb_send_mail($user_mail, "ReNovelパスワード変更(自動送信)", $mail_content);

                        echo '<script>alert("パスワード変更通知を設定時のメールアドレスに送信しました");location.href = "/";</script>';
                    } else {
                        header('Location: ./error=account');
                    }

                } else {
                    header('Location: ./?error=id');
                }
                break;
            case 'password':
                if (isset($_SESSION['forget_password_token']) && isset($_POST['password'])) {
                    $token = $_SESSION['forget_password_token'];

                    if ($Account->forget_password_in_token($token)) {
                        $result = $Account->forget_password_reset_password($token, $_POST['password']);

                        if ($result) {
                            header('Location: ../');
                        } else {
                            header('Location: ./?error=token');
                        }
                    }
                } else {
                    header('Location: ./?error=token');
                }
                break;
        }
    }
}

echo_header($user_data);
?>
<main>
    <section class="forget_password">
        <h2 class="section-title">パスワードリセット</h2>
        <form action="./" method="post">
            <?php if ($mode == "mail"): ?>
                <input type="hidden" name="type" value="mail">
                <label class="label-forget">
                    リセット対象のアカウントID:
                    <input type="text" name="id" placeholder="ID" required>
                </label>
            <?php elseif ($mode == "password"): ?>
                <input type="hidden" name="type" value="password">
                <label class="label-forget">
                    パスワード:
                    <input type="password" name="password" placeholder="Password" required>
                </label>
            <?php endif; ?>
            <br>
            <button type="submit">送信</button>
        </form>
    </section>

</main>