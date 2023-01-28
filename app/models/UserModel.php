<?php

namespace app\models;
use app\core\Model;
use app\core\Route;
use database\CreateDB;
use mysqli;
class UserModel extends Model
{
    /**
     * Let's get all the logins from the database
     * @return array
     */
    public function get() : array
    {
        $stmt = $this->db->prepare("SELECT login FROM users");
        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while ($user = $result->fetch_assoc())
        {
            $users[] = $user;
        }

        return $users;
    }

    public function isUser($user) : bool
    {
        $users = $this->get();
        return in_array($user['login'], array_column($users, 'login'));
    }

    /**
     * Get all users
     * @return array
     */
    public function all()
    {
        $sql = "SELECT * FROM users;";
        $result = $this->db->query($sql);
        if (!$result) {
            // TODO create log
            exit('some problem with select user');
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Add a new user to the database
     * @param array $user
     * @return void
     */
    public function add(array $user) : void
    {
        if (!$this->isUser($user)) {
            $user['main'] = $user['main'] ?? 0;
            $sql = "INSERT INTO users (login, pass, main) 
                    VALUES ('{$user['login']}', '{$user['pass']}', '{$user['main']}');";
            $this->db->query($sql);
            $result = mysqli_affected_rows($this->db) !== 1;
            if ($result) {
                // TODO create log
                exit('some problem with insert user');
            } else {
                header('Location: /admin');
            }
        } else {
            // TODO have user;
        }
    }

    /**
     * Let's delete the user by id from the database
     * @param $id
     * @return void
     */
    public function delete($id) : void
    {
       $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param('i', $_POST['id']);
        $stmt->execute();
    }
}