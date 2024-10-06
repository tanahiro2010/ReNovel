<?php
require_once '../db.php';
require_once '../novel.php';
class NovelManager
{
    private $DataBase;

    /**
     * Constructor for the NovelManager class.
     *
     * Initializes a new instance of the JsonDB class with the path to the novel.json database.
     */
    public function __construct()
    {
        $this->DataBase = new JsonDB('../../db/novel.json');
    }

    public function delete($target): null
    {
        $idFilter = function ($callbackData) use ($target): bool {
            if ($callbackData['id'] === $target['id']) {
                return true;
            }
            return false;
        };
        $targetData = $this->DataBase->fetchWithIndex($idFilter);
        if ($targetData) {
            foreach ($targetData as $data) {
                $this->DataBase->delete($data['index']);
            }
        }
        return null;
    }
}
