<?php
require_once 'db.php';
require_once 'user.php';
class Novel
{
    public $Id;
    public $Author;
    public $Title;
    public $Description;
    public $Genre;
    public $Tags;
    public $Episodes;
    public $Rating;
    public $Status;
    public $Comments;
    public $UpdateAt;
    public $Star;
    public $Watch;
    public $StartAt;
    public $Visibility;
    public $Type;

    private $DataBase;

    /**
     * Constructor for the Novel class.
     *
     * This constructor initializes a Novel object. It can accept either a string ID or an object.
     * If a string ID is provided, it fetches the corresponding novel data from a JSON database.
     * If an object is provided, it directly initializes the novel properties from the object.
     *
     * @param string|object|array $initalizeData The ID of the novel as a string or an object containing novel data.
     */
    public function __construct($initalizeData)
    {
        $this->DataBase = new JsonDB('../db/novel.json');
        if(is_string($initalizeData)){
            $filter = function ($data) use ($initalizeData) {
                return $data['id'] == $initalizeData;
            };
            $data = $this->DataBase->fetch($filter);
            if($data){
                $this->Id = $data['id'];
                $this->Author = $data['author'];
                $this->Title = $data['title'];
                $this->Description = $data['description'];
                $this->Genre = $data['genre'];
                $this->Tags = $data['tags'];
                $this->Episodes = $data['episodes'];
                $this->Rating = $data['rating'];
                $this->Status = $data['status'];
                $this->Comments = $data['comments'];
                $this->UpdateAt = $data['updateAt'];
                $this->StartAt = $data['startAt'];
                $this->Visibility = $data['visibility'];
                $this->Type = $data['type'];
                $this->Star = $data['star'];
                $this->Watch = $data['watch'];
            }
            return;
        }
        if (is_object($initalizeData)) {
            $this->Id = $initalizeData->id;
            $this->Author = $initalizeData->author;
            $this->Title = $initalizeData->title;
            $this->Description = $initalizeData->description;
            $this->Genre = $initalizeData->genre;
            $this->Tags = $initalizeData->tags;
            $this->Episodes = $initalizeData->episodes;
            $this->Rating = $initalizeData->rating;
            $this->Status = $initalizeData->status;
            $this->Comments = $initalizeData->comments;
            $this->UpdateAt = $initalizeData->updateAt;
            $this->StartAt = $initalizeData->startAt;
            $this->Visibility = $initalizeData->visibility;
            $this->Type = $initalizeData->type;
            $this->Star = $initalizeData->star;
            $this->Watch = $initalizeData->watch;
        }
        if (is_array($initalizeData)) {
            $this->Id = $initalizeData['id'];
            $this->Author = $initalizeData['author'];
            $this->Title = $initalizeData['title'];
            $this->Description = $initalizeData['description'];
            $this->Genre = $initalizeData['genre'];
            $this->Tags = $initalizeData['tags'];
            $this->Episodes = $initalizeData['episodes'];
            $this->Rating = $initalizeData['rating'];
            $this->Status = $initalizeData['status'];
            $this->Comments = $initalizeData['comments'];
            $this->UpdateAt = $initalizeData['updateAt'];
            $this->StartAt = $initalizeData['startAt'];
            $this->Visibility = $initalizeData['visibility'];
            $this->Type = $initalizeData['type'];
            $this->Star = $initalizeData['star'];
            $this->Watch = $initalizeData['watch'];
        }
    }
#    /**
#     * @param string $novel_id
#     * @param string|null $title
#     * @param string|null $description
#     * @param string $statue
#     * @param string|null $text
#     * @param string|null $tags
#     * @return mixed|null
#     */
#    public function editNovel(string $novel_id, string $title = null, string $description = null, string $statue = "private", string $text = null, string $tags = null)
#    {
#        if (isset($this->DataBase['novel'][$novel_id])) {
#            $this->DataBase['novel'][$novel_id]['title'] = htmlspecialchars($title ?? $this->DataBase['novel'][$novel_id]['title']);
#            $this->DataBase['novel'][$novel_id]['description'] = htmlspecialchars($description ?? $this->DataBase['novel'][$novel_id]['description']);
#            $this->DataBase['novel'][$novel_id]['tags'] = $tags == null ? $this->DataBase['novel'][$novel_id]['tags'] : explode(" ", $tags);
#            if ($this->DataBase['novel'][$novel_id]['type'] == "short") {
#                $this->DataBase['novel'][$novel_id]['text'] = htmlspecialchars($text ?? $this->DataBase['novel'][$novel_id]['text']);
#            }
#
#            $this->save($this->DataBase);
#
#            if ($statue == "private") {
#                $this->toPrivate_novel($novel_id);
#            } elseif ($statue == "public") {
#                $this->toPublic_novel($novel_id);
#            }
#
#            
#
#            return $this->DataBase['novel'][$novel_id];
#        }
#
#        return null;
#    }
#
#    /**
#     * @param string $novel_id
#     * @return void
#     */
#
#    public function toPrivate_novel(string $novel_id)
#    {
#        
#
#        if (isset($this->DataBase['novel'][$novel_id])) {
#            $this->DataBase['novel'][$novel_id]['status'] = "private";
#
#            $this->save($this->DataBase);
#        }
#    }
#
#    /**
#     * @param string $episode_id
#     * @return bool
#     */
#
#    public function toPrivate_episode(string $episode_id)
#    {
#        
#
#        if (isset($this->DataBase['novel_text'][$episode_id])) {
#            $this->DataBase['novel_text'][$episode_id]['status'] = "private";
#            $this->save($this->DataBase);
#            return true;
#        }
#
#        return false;
#    }
#
#    /**
#     * @param string $novel_id
#     * @return void
#     */
#
#    public function toPublic_novel(string $novel_id)
#    {
#        
#
#        if (isset($this->DataBase['novel'][$novel_id])) {
#            $this->DataBase['novel'][$novel_id]['status'] = "public";
#
#            $this->save($this->DataBase);
#        }
#    }
#
#    /**
#     * @param $episode_id
#     * @return bool
#     */
#
#    public function toPublic_episode($episode_id)
#    {
#        
#
#        if (isset($this->DataBase['novel_text'][$episode_id])) {
#            $this->DataBase['novel_text'][$episode_id]['status'] = "public";
#            $this->save($this->DataBase);
#            return true;
#        }
#
#        return false;
#    }
#
#    /**
#     * @param $novel_id
#     * @param $episode_title
#     * @param $episode_text
#     * @return false|string
#     * @throws \Random\RandomException
#     */
#
#    public function uploadEpisode($novel_id, $episode_title, $episode_text)
#    {
#        
#
#        if (isset($this->DataBase['novel'][$novel_id])) {
#            if ($this->DataBase['novel'][$novel_id]['type'] == "long") {
#                $date = date("Y/m/d H:i:s");
#
#                $episode_id = bin2hex(random_bytes(16));
#                $this->DataBase['novel'][$novel_id]['episodes'][] = $episode_id;
#                $this->DataBase['novel'][$novel_id]['last_update'] = $date;
#
#                $this->DataBase['novel_text'][$episode_id] = array(
#                    'title' => htmlspecialchars($episode_title),
#                    'text' => htmlspecialchars($episode_text),
#                    'status' => 'private',
#                    'author_novel' => $novel_id,
#                    'update_date' => $date
#                );
#
#
#                $this->save($this->DataBase);
#
#                return $episode_id;
#            }
#        }
#
#        return false;
#    }
#
#    /**
#     * @param $episode_id
#     * @param $episode_title
#     * @param $episode_text
#     * @return bool
#     */
#
#    public function editEpisode($episode_id, $episode_title, $episode_text)
#    {
#        
#        if (isset($this->DataBase['novel_text'][$episode_id])) {
#            if ($this->DataBase['novel_text'][$episode_id]) {
#                $this->DataBase['novel_text'][$episode_id]['title'] = htmlspecialchars($episode_title);
#                $this->DataBase['novel_text'][$episode_id]['text'] = htmlspecialchars($episode_text);
#                $this->DataBase['novel_text'][$episode_id]['update_date'] = date("Y/m/d H:i:s");
#
#                $this->save($this->DataBase);
#
#                return true;
#            }
#        }
#
#        return false;
#    }
#
#    /**
#     * @param string $novel_id
#     * @return bool
#     */
#
#    public function deleteNovel($novel_id)
#    {
#        
#
#        if (isset($this->DataBase['novel'][$novel_id])) {
#            $novel_data = $this->DataBase['novel'][$novel_id];
#
#            if ($novel_data['type'] == "long") {
#                $episodes = $novel_data['episodes'];
#
#                foreach ($episodes as $episode) {
#                    unset($this->DataBase['novel_text'][$episode]);
#                }
#            }
#
#            unset($this->DataBase['novel'][$novel_id]); // 小説のデータを削除
#
#            $this->save($this->DataBase);
#            $this->Account->deleteArrayFromValue($novel_data['author_id'], 'my_novel', $novel_id);
#
#            return true;
#        }
#
#        return false;
#    }
#
#    /**
#     * @param string $novel_id
#     * @param string $episode_id
#     * @return bool
#     */
#
#    public function deleteEpisode($novel_id, $episode_id)
#    {
#        
#
#        if (isset($this->DataBase['novel'][$novel_id])) {
#            if (in_array($episode_id, $this->DataBase['novel'][$novel_id]['episodes'])) {
#                unset($this->DataBase['novel_text'][$episode_id]);
#                $this->DataBase['novel'][$novel_id]['episodes'] = $this->delete($this->DataBase['novel'][$novel_id]['episodes'], $episode_id);
#
#                $this->save($this->DataBase);
#
#                return true;
#            }
#        }
#
#        return false;
#    }
#
#    /**
#     * @param $novel_id
#     * @return int|mixed
#     */
#
#    public function get_watch($novel_id)
#    {
#        
#
#        return $this->DataBase['novel'][$novel_id]['watch'] ?? 0;
#    }
#
#    /**
#     * @param string $user_id
#     * @param string $novel_id
#     * @return int
#     */
#
#    public function add_watch(string $user_id, string $novel_id)
#    {
#        
#
#        if (isset($this->DataBase['novel'][$novel_id])) {
#            if (!in_array($user_id, $this->DataBase['novel'][$novel_id]['watch'])) {
#                $this->DataBase['novel'][$novel_id]['watch'][] = $user_id;
#            }
#        }
#
#        $this->save($this->DataBase);
#
#        return count($this->DataBase['novel'][$novel_id]['watch']);
#    }
#
#    public function get_novels()
#    {
#        
#        return $this->DataBase['novel'];
#    }
#
#    /**
#     * @param string $user_id
#     * @param string $novel_id
#     * @return bool
#     */
#    public function follow_novel(string $user_id, string $novel_id)
#    {
#        $mode = "follow";
#        if (in_array($novel_id, $this->Account->fetch($user_id, 'follow-novel'))) {
#            $this->Account->deleteArrayFromValue($user_id, 'follow-novel', $novel_id);
#            $mode = "unfollow";
#        } else {
#            $this->Account->appendArray($user_id, 'follow-novel', $novel_id);
#            $mode = "follow";
#        }
#
#        
#        $ret = false;
#        if ($mode == "unfollow") {
#            $this->DataBase['novel'][$novel_id]['followers'] = $this->delete($this->DataBase['novel'][$novel_id]['followers'], $user_id);
#        } else {
#            $this->DataBase['novel'][$novel_id]['followers'][] = $user_id;
#            $ret = true;
#        }
#
#        $this->save($this->DataBase);
#        return $ret;
#    }
#
#    public function isFollow($user_id, $novel_id)
#    {
#        return in_array($novel_id, $this->Account->fetch($user_id, 'follow-novel'));
#    }
#
#    /**
#     * @param string $novel_id
#     * @return mixed|null
#     */
#
#    public function fetch_novel(string $novel_id)
#    {
#        return $this->DataBase['novel'][$novel_id] ?? null;
#    }
#
#    /**
#     * @param string $episode_id
#     * @return mixed|null
#     */
#
#    public function fetch_episode(string $episode_id)
#    {
#        
#
#        return $this->DataBase['novel_text'][$episode_id] ?? null;
#    }
}
