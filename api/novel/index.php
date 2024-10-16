<?php
require_once '../../functions/app.php';
$Account = new DataBase('../../db/database.json', 'novel');
$Novel = new Novel('../../db/database.json', 'novel');

$user_data = $Account->isLogin();
$user_id = $user_data['id'];

header('Content-type: application/json');

if ($user_data) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') { // 投稿
        if (isset($_POST['title']) && isset($_POST['description']) && isset($_POST['genre']) && isset($_POST['type'])) {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $genre = $_POST['genre'];
            $type = $_POST['type'] == 'short' || $_POST['type'] == 'long' ? $_POST['type'] : 'long';
            $tags = $_POST['tags'] ?? '';
            $text = '';
            if ($type == 'short') {
                $text = $_POST['text'] ?? '';

                if (strlen($text) == 0) {
                    echo json_encode(array('status' => 'error', 'message' => 'text is empty'));
                    exit();
                }
            }

            $novel_id = $Novel->createNovel($user_id, $title, $description, $genre, $type, $tags, $text);
            echo json_encode(array('status' => 'success', 'novel_id' => $novel_id), JSON_PRETTY_PRINT);

        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') { // 削除
         parse_str(file_get_contents("php://input"), $_DELETE);
         $novel_id = $_DELETE['novel'];
         $novel_data = $Novel->fetch_novel($novel_id);
         if ($novel_data) { // 小説が存在するなら
             if ($user_id == $novel_data['author_id']) { // 作者が一緒なら
                 $Novel->deleteNovel($novel_id);
                 echo json_encode(array('status' => 'success'), JSON_PRETTY_PRINT);
             } else {                                    // 作者ちがうなら
                 header('HTTP/1.0 400 Bad Request');
                 echo json_encode(array('status' => 'error', 'message' => 'author is not you.'), JSON_PRETTY_PRINT);
             }
         } else {           // 小説が存在しないなら
             header('HTTP/1.0 400 Bad Request');
             echo json_encode(array('status' => 'error', 'message' => 'novel not found.'), JSON_PRETTY_PRINT);
         }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'GET') { // 小説情報取得
    if (isset($_GET['novel'])) {
        $novel_id = $_GET['novel'];
        $novel_data = $Novel->fetch_novel($novel_id);

        if ($novel_data) {
            echo json_encode(array('status' => 'success', 'novel_data' => $novel_data), JSON_PRETTY_PRINT);
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'novel not found.'), JSON_PRETTY_PRINT);
        }

    }
} elseif (!$user_data) {
    header('HTTP/1.0 401 Unauthorized');
}