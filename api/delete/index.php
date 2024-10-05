<?php
require_once '../../functions/app.php';
require_once '../../functions/header.php';

$Account = new DataBase('../../db/database.json', 'novel');
$Novel = new Novel('../../db/database.json', 'novel');

$user_data = $Account->isLogin();

if ($user_data) {
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        if (isset($_GET['type'])) {
            $redirect_url = $_GET['redirect_url'] ?? '/';
            switch ($_GET['type']) {
                case 'novel':
                    if (isset($_GET['novel'])) {
                        $novel_id = $_GET['novel'];
                        $novel_data = $Novel->fetch_novel($novel_id);

                        if ($novel_data) {
                            $novels = $Account->fetch($user_data['id'], 'my_novel');
                            if (in_array($novel_id, $novels)) {
                                $result = $Novel->deleteNovel($novel_id);
                            } else {
                                header("HTTP/1.0 503 Service Unavailable");
                            }
                        } else {
                            header("HTTP/1.0 404 Not Found");
                        }
                    } else {
                        header("HTTP/1.1 400 Bad Request");
                    }
                    break;
                case 'episode':
                    if (isset($_GET['episode'])) {
                        $episode_id = $_GET['episode'];
                        $episode_data = $Novel->fetch_episode($episode_id);
                        $novel_id = $episode_data['author_novel'];
                        $novel_data = $Novel->fetch_episode($novel_id);

                        if ($episode_data) {
                            $novels = $Account->fetch($user_data['id'], 'my_novel');
                            if (in_array($novel_id, $novels)) {
                                $Novel->deleteEpisode($novel_id, $episode_id);
                            } else {
                                header("HTTP/1.0 503 Service Unavailable404");
                            }
                        } else {
                            header('HTTP/1.0 404 Not Found');
                        }
                    }
                    break;
            }

            header('Location: ' . $redirect_url);
        }
    } else {
        header("HTTP/1.1 405 Method Not Allowed");
    }
} else {
    header('HTTP/1.1 401 Unauthorized');
    header('Location: /');
}
exit();