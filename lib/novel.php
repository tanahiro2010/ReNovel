<?php
require_once 'db.php';
require_once 'user.php';
class Novel
{
    public string $Id;
    public User $Author;
    public string $Title;
    public string $Description;
    public $Genre;
    public array $Tags;
    public array $Episodes;
    public float $Rating;
    public $Status;
    public array $Comments;
    public string $UpdateAt;
    public array $Star;
    public float $Watch;
    public string $StartAt;
    public $Visibility;
    public $Type;
    private bool $isConstructed = false;

    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            if ($this->isConstructed) {
                $this->UpdateAt = date('Y-m-d H:i:s');
                $this->patchNovel();
            }
            $this->$name = $value;
        } else {
            trigger_error("Undefined property: " . __CLASS__ . "::$name", E_USER_NOTICE);
        }
    }

    private JsonDB $DataBase;

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
        if (is_string($initalizeData)) {
            $filter = function ($data) use ($initalizeData) {
                return $data['id'] == $initalizeData;
            };
            $data = $this->DataBase->fetch($filter);
            if ($data) {
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
        $this->isConstructed = true;
    }
    /**
     * @return mixed|null
     */
    private function patchNovel()
    {
        $index = $this->DataBase->fetchWithIndex(
            function ($data) use ($this) {
            return $data['id'] == $this->Id;
        });
        if ($index) {
            $this->DataBase->update($index[0]['index'], $this);
            return;
        }
        return null;
    }
}
