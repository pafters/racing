<?php
class Users
{
    function __construct($db)
    {
        $this->db = $db;
    }

    public function login($login, $password) {
        $user = $this->db->getUser($login);
        if ($user) {
            $user->token = $this->db->addToken($user->id);
            if ($password == $user->password) {
                $token = $user->token; //
                setcookie('token',$token, 0, '/'); //добавили куки
                return $user;
            }
        }
    }

    public function signup($name, $login, $password)
    {
        $this->db->addUser($name, $login, $password);
        $user = $this->db->getUser($login);
        if ($user) {
            return array(
                'status' => 'ok',
                'name' => $user->name,
            );
        }
    }

    public function logout($token)
    {
        $this->db->removeToken($token);
        setcookie('token', null, -1, '/');
    }

    public function getUserByToken($token) {
        $user = $this->db->getUserByToken($token);
        if ($user) {
            return array(
                'name' => $user->name,
                'token' => $user->token
            );; 
        }
    }

    public function getUser($token) {
        return $this->db->getUserByToken($token);
    }

    public function checklog($login) //Проверка на наличие логина в базе данных при регистрации
    {
        $user = $this->db->getUser($login);
        if($user) {
            return array(
                'status' => false,
            );
        } else {
            return array(
                'status' => true
            );
        }
    }

}
