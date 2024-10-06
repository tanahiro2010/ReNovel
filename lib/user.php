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
}
