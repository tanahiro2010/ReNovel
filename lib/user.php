<?php
require_once './db.php';
class User
{
    public $id;
    public $name;
    public $mail;
    public $password;
    public $followers;
    public $following;

    /**
     * Constructor for the User class.
     *
     * This constructor can initialize a User object either from an associative array
     * or from individual parameters.
     *
     * @param mixed $id User ID or an associative array containing user details.
     * @param string|null $name User's name.
     * @param string|null $mail User's email address.
     * @param string|null $password User's password.
     * @param array $followers List of followers.
     * @param array $following List of users being followed.
     */
    public function __construct($id = null, $name = null, $mail = null, $password = null, $followers = [], $following = [])
    {
        if (is_array($id)) {
            $this->id = $id['id'];
            $this->name = $id['name'];
            $this->mail = $id['mail'];
            $this->password = $id['password'];
            $this->followers = $id['followers'];
            $this->following = $id['following'];
        } else {
            $this->id = $id;
            $this->name = $name;
            $this->mail = $mail;
            $this->password = $password;
            $this->followers = $followers;
            $this->following = $following;
        }
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
    public function register($id, $name, $mail, $password): array
    {
        $account = new JsonDB('../db/user.json');
        if ($this->exists(['id' => $id])) {
            return [];
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
        $this->__construct($data);
        return $data;
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
}
