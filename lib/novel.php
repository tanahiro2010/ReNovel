<?php
require_once 'db.php';
require_once 'user.php';
class Novel
{
    public $Author;
    function __construct($AuthorParam)
    {
        if (is_string($this->$AuthorParam)) {
            $this->Author = new User($AuthorParam);
        } elseif (is_object($AuthorParam)) {
            $this->Author = new User($AuthorParam);
        }
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
    public function createNovel(string $author_id, string $title, string $description, string $genre, string $tags, string $type = "long", string $text = "")
    {
        $database = $this->load();
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

        $new_entry = array($novel_id => $novelObj);
        $database['novel'] = $new_entry + $database['novel'];

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
        $database = $this->load();
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
            if (!in_array($user_id, $database['novel'][$novel_id]['watch'])) {
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
