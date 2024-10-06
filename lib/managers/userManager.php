<?php
require_once '../db.php';
require_once '../user.php';
class UserManager
{
    private $DataBase;
    public function __construct()
    {
        $this->DataBase = new JsonDB('../db/user.json');
    }
    /**
     * Registers a new user with the provided details.
     *
     * @param int $id The unique identifier for the user.
     * @param string $name The name of the user.
     * @param string $mail The email address of the user.
     * @param string $password The password for the user account.
     * @return array An array containing the registered user data, or an empty array if the user already exists.
     */
    public function create($id, $name, $mail, $password): ?User
    {
        $account = new JsonDB('../db/user.json');
        if ($this->exists(['id' => $id])) {
            return;
        }

        $data = [
            'id' => $id,
            'name' => $name,
            'mail' => $mail,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'followers' => [],
            'following' => [],
        ];
        $account->insert($data);
        return new User($data);
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
        $account = new JsonDB('../db/user.json');
        $idFilter = function ($callbackData) use ($data): bool {
            if ($callbackData['id'] === $data['id']) {
                return true;
            }
            return false;
        };
        $result = $account->fetch($idFilter);
        return count($result) > 0;
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
