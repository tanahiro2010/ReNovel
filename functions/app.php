<?php
session_start();

class DataBase
{
    private $DB_PATH;
    private $AppName;

    /**
     * @param $DB_PATH
     * @param $AppName
     */
    function __construct($DB_PATH, $AppName)
    {
        $this->DB_PATH = $DB_PATH;
        $this->AppName = $AppName;
    }

    private function load()
    {
        return json_decode(file_get_contents($this->DB_PATH), true);
    }

    private function save($database): bool|int
    {
        return file_put_contents($this->DB_PATH, json_encode($database, JSON_PRETTY_PRINT));
    }

    /**
     * @param string $id
     * @param string $name
     * @param string $mail
     * @param string $password
     * @return array|bool
     */

    public function register($id, $name, $mail, $password): array|bool
    {
        $database = $this->load();

        if (isset($database['user'][$id])) {
            return false;
        } else {
            // ユーザーオブジェクト作成
            $userObj = array(
                'id' => htmlspecialchars($id),
                'name' => htmlspecialchars($name),
                'mail' => $mail,
                'password' => password_hash($password, PASSWORD_DEFAULT),

                // 小説の是非に
                'followers' => array(),
                'following' => array(),

                'my_novel' => array(),
                'follow-novel' => array()
            );

            // ユーザーデータをデータベースに保存
            $database['user'][$id] = $userObj;
            $this->save($database);

            // セッションにログインデータを保存
            $_SESSION[$this->AppName]['user'] = $userObj;

            return $userObj;
        }
    }

    /**
     * @param string $id
     * @param string $password
     * @return bool|mixed
     */

    public function login($id, $password): bool|array
    {
        $database = $this->load();

        if (isset($database['user'][$id])) { // ユーザーが存在するなら
            $userObj = $database['user'][$id];
            if (password_verify($password, $userObj['password'])) {
                // セッションにログイン情報を保存
                $_SESSION[$this->AppName]['user'] = $userObj;
                return $userObj;
            }
        }

        return false;
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        session_destroy();
    }

    /**
     * @param string $user_id
     * @return false|string
     * @throws \Random\RandomException
     */

    public function forget_password_create_token($user_id): bool|string
    {
        $database = $this->load();

        if (isset($database['user'][$user_id])) {
            $token = bin2hex(random_bytes(32));
            $database['forget_password'][$token] = $user_id;

            $this->save($database);

            return $token;
        } else {
            return false;
        }
    }

    public function forget_password_reset_password($token, $password): bool
    {
        $database = $this->load();

        if (isset($database['forget_password'][$token])) {
            $user_id = $database['forget_password'][$token];
            $database['user'][$user_id]['password'] = password_hash($password, PASSWORD_DEFAULT);

            unset($database['forget_password'][$token]);

            $this->save($database);
            return true;
        } else {
            return false;
        }
    }

    public function forget_password_in_token($token): bool
    {
        $database = $this->load();
        return isset($database['forget_password'][$token]);
    }

    /**
     * @return false|mixed
     */

    public function isLogin(): bool|array
    {
        $database = $this->load();
        if (isset($_SESSION[$this->AppName]['user'])) {
            $userId = $_SESSION[$this->AppName]['user']['id'];

            if (isset($database['user'][$userId])) {
                return $database['user'][$userId];
            }
        }

        return false;
    }

    /**
     * @param $user_id
     * @return bool|mixed|array
     */

    public function in_account($user_id): bool|array
    {
        $database = $this->load();

        return $database['user'][$user_id] ?? false;
    }

    /**
     * @param $userId
     * @param $key
     * @param $value
     * @return false|mixed
     */

    public function appendArray($userId, $key, $value)
    {
        $database = $this->load();

        if (isset($database['user'][$userId])) {
            $database['user'][$userId][$key][] = $value;

            $this->save($database);

            return $database['user'][$userId][$key];
        }

        return false;
    }

    public function appendMixed($userId, $key, $mixedKey, $mixedValue)
    {
        $database = $this->load();

        if (isset($database['user'][$userId])) {
            $database['user'][$userId][$key][$mixedKey] = $mixedValue;

            $this->save($database);

            return $database['user'][$userId][$key];
        }

        return false;
    }

    public function deleteArrayToAll($userId, $key)
    {
        $database = $this->load();

        if (isset($database['user'][$userId])) {
            $no = 0;

            foreach ($database['user'][$userId] as $value) {
                if ($key == $value) {
                    unset($database['user'][$userId][$key]);

                    $this->save($database);

                    return $database['user'][$userId][$key];
                }
                $no += 1;
            }
        }

        return false;
    }

    /**
     * @param $userId
     * @param $key
     * @param $deleteTargetKey
     * @return null
     */

    public function deleteArrayFromKey($userId, $key, $deleteTargetKey)
    {
        $database = $this->load();

        if (isset($database['user'][$userId][$key])) {
            unset($database['user'][$userId][$key][$deleteTargetKey]);

            $this->save($database);
        }

        return null;
    }

    /**
     * @param $userId
     * @param $key
     * @param $deleteTargetValue
     * @return mixed|null
     */

    public function deleteArrayFromValue($userId, $key, $deleteTargetValue): array|null
    {
        $database = $this->load();

        if (isset($database['user'][$userId][$key])) {
            foreach ($database['user'][$userId][$key] as $array_key => $value) {
                if ($deleteTargetValue == $value) {
                    unset($database['user'][$userId][$key][$array_key]);

                    $this->save($database);

                    return $database['user'][$userId][$key];
                }
            }
        }

        return null;
    }

    /**
     * @param string $userId
     * @param string $key
     * @return mixed|null
     */
    public function fetch($userId, $key)
    {
        $database = $this->load();

        if (isset($database['user'][$userId])) {
            return $database['user'][$userId][$key];
        }

        return null;
    }
}

class Novel
{
    private $DB_PATH;
    private $Account;
    function __construct($DB_PATH, $AppName)
    {
        $this->DB_PATH = $DB_PATH;
        $this->Account = new DataBase($this->DB_PATH, $AppName);
    }

    private function load()
    {
        return json_decode(file_get_contents($this->DB_PATH), true);
    }

    private function save($database)
    {
        file_put_contents($this->DB_PATH, json_encode($database, JSON_PRETTY_PRINT));
    }

    private function delete($target, $index)
    {

        foreach ($target as $key => $value) {
            if ($index == $value) {
                unset($target[$key]);
                return $target;
            }
        }

        return null;
    }

    /**
     * @param string $author_id
     * @param string $title
     * @param string $description
     * @param string $genre
     * @param string $tags
     * @param string $type
     * @param string $text
     * @return string
     * @throws \Random\RandomException
     */
    public function createNovel(string $author_id, string $title, string $description, string $genre, string $tags, string $type="long", string $text="")
    {
        $database  = $this->load();
        $novel_id = bin2hex(random_bytes(16));

        $novelObj = array(
            'title' => htmlspecialchars($title),
            'description' => htmlspecialchars($description),
            'author_id' => $author_id,
            'genre' => $genre,
            'tags' => explode(" ", $tags),
            'type' => $type,
            'status' => 'private',
            'last_update' => date("Y-m-d H:i:s"),

            'watch' => array(),
            'totalPoint' => array(),
            'followers' => array()
        );

        if ($type == "long") {
            $novelObj['episodes'] = array();
        } elseif ($type == "short") {
            $novelObj['text'] = htmlspecialchars($text);
        }

        $database['novel'][$novel_id] = $novelObj;
        $this->save($database);
        $this->Account->appendArray($author_id, 'my_novel', $novel_id);

        return $novel_id;
    }

    /**
     * @param string $novel_id
     * @param string|null $title
     * @param string|null $description
     * @param string $statue
     * @param string|null $text
     * @param string|null $tags
     * @return mixed|null
     */

    public function editNovel(string $novel_id, string $title = null, string $description = null, string $statue = "private", string $text = null, string $tags = null)
    {
        $database  = $this->load();
        if (isset($database['novel'][$novel_id])) {
            $database['novel'][$novel_id]['title'] = htmlspecialchars($title ?? $database['novel'][$novel_id]['title']);
            $database['novel'][$novel_id]['description'] = htmlspecialchars($description ?? $database['novel'][$novel_id]['description']);
            $database['novel'][$novel_id]['tags'] = $tags == null ? $database['novel'][$novel_id]['tags'] : explode(" ", $tags);
            if ($database['novel'][$novel_id]['type'] == "short") {
                $database['novel'][$novel_id]['text'] = htmlspecialchars($text ?? $database['novel'][$novel_id]['text']);
            }

            $this->save($database);

            if ($statue == "private") {
                $this->toPrivate_novel($novel_id);
            } elseif ($statue == "public") {
                $this->toPublic_novel($novel_id);
            }

            $database = $this->load();

            return $database['novel'][$novel_id];
        }

        return null;
    }

    /**
     * @param string $novel_id
     * @return void
     */

    public function toPrivate_novel(string $novel_id)
    {
        $database = $this->load();

        if (isset($database['novel'][$novel_id])) {
            $database['novel'][$novel_id]['status'] = "private";

            $this->save($database);
        }
    }

    /**
     * @param string $episode_id
     * @return bool
     */

    public function toPrivate_episode(string $episode_id)
    {
        $database = $this->load();

        if (isset($database['novel_text'][$episode_id])) {
            $database['novel_text'][$episode_id]['status'] = "private";
            $this->save($database);
            return true;
        }

        return false;
    }

    /**
     * @param string $novel_id
     * @return void
     */

    public function toPublic_novel(string $novel_id)
    {
        $database = $this->load();

        if (isset($database['novel'][$novel_id])) {
            $database['novel'][$novel_id]['status'] = "public";

            $this->save($database);
        }
    }

    /**
     * @param $episode_id
     * @return bool
     */

    public function toPublic_episode($episode_id)
    {
        $database = $this->load();

        if (isset($database['novel_text'][$episode_id])) {
            $database['novel_text'][$episode_id]['status'] = "public";
            $this->save($database);
            return true;
        }

        return false;
    }

    /**
     * @param $novel_id
     * @param $episode_title
     * @param $episode_text
     * @return false|string
     * @throws \Random\RandomException
     */

    public function uploadEpisode($novel_id, $episode_title, $episode_text)
    {
        $database = $this->load();

        if (isset($database['novel'][$novel_id])) {
            if ($database['novel'][$novel_id]['type'] == "long") {
                $date = date("Y/m/d H:i:s");

                $episode_id = bin2hex(random_bytes(16));
                $database['novel'][$novel_id]['episodes'][] = $episode_id;
                $database['novel'][$novel_id]['last_update'] = $date;

                $database['novel_text'][$episode_id] = array(
                    'title' => htmlspecialchars($episode_title),
                    'text' => htmlspecialchars($episode_text),
                    'status' => 'private',
                    'author_novel' => $novel_id,
                    'update_date' => $date
                );


                $this->save($database);

                return $episode_id;
            }
        }

        return false;
    }

    /**
     * @param $episode_id
     * @param $episode_title
     * @param $episode_text
     * @return bool
     */

    public function editEpisode($episode_id, $episode_title, $episode_text)
    {
        $database = $this->load();
        if (isset($database['novel_text'][$episode_id])) {
            if ($database['novel_text'][$episode_id]) {
                $database['novel_text'][$episode_id]['title'] = htmlspecialchars($episode_title);
                $database['novel_text'][$episode_id]['text'] = htmlspecialchars($episode_text);
                $database['novel_text'][$episode_id]['update_date'] = date("Y/m/d H:i:s");

                $this->save($database);

                return true;
            }
        }

        return false;
    }

    /**
     * @param string $novel_id
     * @return bool
     */

    public function deleteNovel($novel_id)
    {
        $database = $this->load();

        if (isset($database['novel'][$novel_id])) {
            $novel_data = $database['novel'][$novel_id];

            if ($novel_data['type'] == "long") {
                $episodes = $novel_data['episodes'];

                foreach ($episodes as $episode) {
                    unset($database['novel_text'][$episode]);
                }
            }

            unset($database['novel'][$novel_id]); // 小説のデータを削除

            $this->save($database);
            $this->Account->deleteArrayFromValue($novel_data['author_id'], 'my_novel', $novel_id);

            return true;
        }

        return false;
    }

    /**
     * @param string $novel_id
     * @param string $episode_id
     * @return bool
     */

    public function deleteEpisode($novel_id, $episode_id)
    {
        $database = $this->load();

        if (isset($database['novel'][$novel_id])) {
            if (in_array($episode_id, $database['novel'][$novel_id]['episodes'])) {
                unset($database['novel_text'][$episode_id]);
                $database['novel'][$novel_id]['episodes'] = $this->delete($database['novel'][$novel_id]['episodes'], $episode_id);

                $this->save($database);

                return true;
            }
        }

        return false;
    }

    /**
     * @param $novel_id
     * @return int|mixed
     */

    public function get_watch($novel_id)
    {
        $database = $this->load();

        return $database['novel'][$novel_id]['watch'] ?? 0;
    }

    /**
     * @param string $user_id
     * @param string $novel_id
     * @return int
     */

    public function add_watch(string $user_id, string $novel_id)
    {
        $database = $this->load();

        if (isset($database['novel'][$novel_id])) {
            if (in_array($user_id, $database['novel'][$novel_id]['watch'])) {
                $database['novel'][$novel_id]['watch'][] = $user_id;
            }
        }

        $this->save($database);

        return count($database['novel'][$novel_id]['watch']);
    }

    public function get_novels()
    {
        $database = $this->load();
        return $database['novel'];
    }

    /**
     * @param string $user_id
     * @param string $novel_id
     * @return bool
     */
    public function follow_novel(string $user_id, string $novel_id)
    {
        $mode = "follow";
        if (in_array($novel_id, $this->Account->fetch($user_id, 'follow-novel'))) {
            $this->Account->deleteArrayFromValue($user_id, 'follow-novel', $novel_id);
            $mode = "unfollow";
        } else {
            $this->Account->appendArray($user_id, 'follow-novel', $novel_id);
            $mode = "follow";
        }

        $database = $this->load();
        $ret = false;
        if ($mode == "unfollow") {
            $database['novel'][$novel_id]['followers'] = $this->delete($database['novel'][$novel_id]['followers'], $user_id);
        } else {
            $database['novel'][$novel_id]['followers'][] = $user_id;
            $ret = true;
        }

        $this->save($database);
        return $ret;

    }

    public function isFollow($user_id, $novel_id)
    {
        return in_array($novel_id, $this->Account->fetch($user_id, 'follow-novel'));
    }

    /**
     * @param string $novel_id
     * @return mixed|null
     */

    public function fetch_novel(string $novel_id)
    {
        $database = $this->load();

        return $database['novel'][$novel_id] ?? null;
    }

    /**
     * @param string $episode_id
     * @return mixed|null
     */

    public function fetch_episode(string $episode_id)
    {
        $database = $this->load();

        return $database['novel_text'][$episode_id] ?? null;
    }
}

function alert($text)
{
    echo '<script>alert("'.$text.'");</script>';
}

function getCurrentURL() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    $params = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';

    return $protocol . "://" . $host . $script . $params;
}

function convertUrlsToLinks($text) {
    // 正規表現パターン
    $urlPattern = '/(https?:\/\/[^\s]+)/i';

    // preg_replace_callbackを使用して、URLを<a>タグで置き換える
    $textWithLinks = preg_replace_callback(
        $urlPattern,
        function($matches) {
            $url = $matches[0];
            return '<a href="' . htmlspecialchars($url) . '" target="_blank">' . htmlspecialchars($url) . '</a>';
        },
        $text
    );

    return $textWithLinks;
}

function convertMentionsToLinks($text) {
    // @から始まるユーザーIDをリンクに変換する正規表現
    $pattern = '/@([a-zA-Z0-9_]+)/';
    // 変換後のリンク形式
    $replacement = '<a href="/user/?id=$1">@$1</a>';

    $user_id = '$1';
    $user_id = preg_replace($pattern, $user_id, $text);



    // 正規表現で置換
    return preg_replace($pattern, $replacement, $text);
}

function convertHashtagsToLinks($text)
{
    // 日本語 (ひらがな、カタカナ、漢字、英数字、アンダースコア) に対応した正規表現
    $pattern = '/#([a-zA-Z0-9_\p{Hiragana}\p{Katakana}\p{Han}]+)/u';

    // リンクに変換
    $replacement = '<a href="/hashtag/?tag=$1">#$1</a>';
    $text = preg_replace($pattern, $replacement, $text);

    return $text;
}

function all_convert($text)
{
    return str_replace("\n", "<br>", convertUrlsToLinks($text));
}

function extractHashtags($text) {
    // 正規表現でハッシュタグを抽出
    preg_match_all('/#\w+/u', $text, $matches);
    // 重複を削除し、結果を返す
    return array_unique($matches[0]);
}