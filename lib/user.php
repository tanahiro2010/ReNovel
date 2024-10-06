<?php
require_once './db.php';
class User
{
    public string $Id;
    public string $Name;
    public string $Mail;
    public string $Password;
    public array $Followers;
    public array $Following;

    private JsonDB $DataBase;

    private $isConstructed = false;

    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            if ($this->isConstructed) {
                $this->UpdateAt = date('Y-m-d H:i:s');
                $this->patchUser();
            }
            $this->$name = $value;
        } else {
            trigger_error("Undefined property: " . __CLASS__ . "::$name", E_USER_NOTICE);
        }
    }

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
    public function __construct(string|object|array $id)
    {
        $this->DataBase = new JsonDB('../db/user.json');
        if (is_array($id)) {
            $this->id = $id['id'];
            $this->name = $id['name'];
            $this->mail = $id['mail'];
            $this->password = $id['password'];
            $this->followers = $id['followers'];
            $this->following = $id['following'];
        }
        if (is_string($id)) {
            $this->id = $id;
        }
        if (is_object($id)) {
            $this->id = $id->id;
            $this->name = $id->name;
            $this->mail = $id->mail;
            $this->password = $id->password;
            $this->followers = $id->followers;
            $this->following = $id->following;
        }
        $this->isConstructed = true;
    }
    private function patchUser()
    {
        $index = $this->DataBase->fetchWithIndex(
            function ($data) use ($this) {
                return $data['id'] == $this->Id;
            }
        );
        if ($index) {
            $this->DataBase->update($index[0]['index'], $this);
            return;
        }
        return;
    }
}
