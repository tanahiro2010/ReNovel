<?php
require_once '../../functions/app.php';
$Account = new DataBase('../../db/database.json', 'novel');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $user_data = $Account->isLogin();
    $my_id = $user_data['id'];
    $redirect_url = $_GET["redirect_url"] ?? "/";

    if ($user_data) { // ログインしてるなら
        if (isset($_GET['type'])) {
            switch ($_GET['type']) {
                case 'user':
                    if (isset($_GET['user'])) {
                        $target_user = $_GET['user'];

                        if ($Account->in_account($target_user)) { // そのユーザーが存在するなら
                            $isFollow = (
                                in_array($my_id, $Account->fetch($target_user, 'followers')) && // 相手側に自分がフォローしたと記録されているか
                                in_array($target_user, $Account->fetch($my_id, 'following'))    // 自分が相手をfollowしたと記録されている
                            );

                            if ($isFollow) { // フォローしているなら
                                $Account->deleteArrayFromValue($target_user, 'followers', $my_id); // 相手のフォロワーデータから自分のIDを削除
                                $Account->deleteArrayFromValue($my_id, 'following', $target_user); // 自分のフォロー中データから相手のIDを削除
                            } else {         // フォローしていないなら
                                $Account->appendArray($target_user, 'followers', $my_id); // 相手のフォロワーデータに自分を追加
                                $Account->appendArray($my_id, 'following', $target_user); // 自分のフォロー中ユーザーデータに自分を追加
                            }
                        }
                    }
                    break;

                case 'novel':
                    if (isset($_GET['novel'])) {

                    }
            }
        }

    }

    header("Location: " . $redirect_url);
} else {
    header("HTTP/1.1 405 Method Not Allowed");
}