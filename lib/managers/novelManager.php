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

    public function create($id, $title, $author, $genre, $status, $rating, $description): ?Novel
    {
        if ($this->exists(['id' => $id])) {
            throw new Exception('Novel already exists.');
        }

        $data = [
            'id' => $id,
            'title' => $title,
            'author' => $author,
            'genre' => $genre,
            'status' => $status,
            'rating' => $rating,
            'description' => $description,
            'comments' => [],
        ];
        $this->DataBase->insert($data);
        return new Novel($data);
    }

    /**
     * Deletes the target data from the database.
     *
     * This method searches for the target data in the database using the provided
     * target identifier and deletes all matching entries.
     *
     * @param array $target The target data to be deleted, containing an 'id' key.
     * @return null Always returns null.
     */
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

    /**
     * Checks if a user exists in the JSON database.
     *
     * This method searches for a user in the JSON database located at '../db/user.json'
     * by matching the provided data's 'id' field with the 'id' field in the database.
     *
     * @param array $data An associative array containing the user data to check. 
     *                    It must include an 'id' key.
     * @return bool Returns true if a user with the given 'id' exists in the database, 
     *              otherwise returns false.
     */
    public function exists($data): bool
    {
        $idFilter = function ($callbackData) use ($data): bool {
            if ($callbackData['id'] === $data['id']) {
                return true;
            }
            return false;
        };
        $result = $this->DataBase->fetch($idFilter);
        return count($result) > 0;
    }
}
