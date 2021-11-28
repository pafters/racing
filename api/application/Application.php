<?php

require_once('db/DB.php');
require_once('users/Users.php');
require_once('arrival/Arrival.php');

class Application {
    function __construct() {
        $db = new DB();
        $this->users = new Users($db);
        $this->arrival = new Arrival($db);
    }

    public function login($params) {
        if ($params['login'] && $params['password']) {
            $user = $this->users->login(
                $params['login'], 
                $params['password']
            );
            if ($user) {
                $this->arrival->setRacer($user->id);
                return array(
                    'name' => $user->name,
                    'token' => $user->token
                );
            }
        }
    }

    public function signup($params)
    {
        if ($params['name'] && $params['login'] && $params['password']) {
            return $this->users->signup($params['name'], $params['login'], $params['password']);
        }
    }

    public function logout($params)
    {
        return $this->users->logout($params['token']);
    }

    public function checklog($params)
    {
        return $this->users->checklog($params['login']);
    }

    public function checkCookie()
    {
        if($_COOKIE) {
            $token = $_COOKIE['token']; 
            return $this->users->getUserByToken($token);
        }
    }

    public function getRaces($params) {
        if ($params['token']) {
            $user = $this->users->getUser($params['token']);
            if ($user) {
                $this->arrival->getRaces();
            }
        }
    }
}
