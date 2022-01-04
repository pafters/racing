<?php

require_once('db/DB.php');
require_once('users/Users.php');
require_once('arrival/Arrival.php');
require_once('lobby/Lobby.php');

class Application
{
    function __construct()
    {
        $db = new DB();
        $this->users = new Users($db);
        $this->arrival = new Arrival($db);
        $this->lobby = new Lobby($db);
    }

    private function getUser($params)
    {
        if ($params['token']) {
            return $this->users->getUserByToken($params['token']);
        }
        return null;
    }

    /***************/
    /*****users*****/
    /***************/

    public function login($params)
    {
        if ($params['login'] && $params['password']) {
            $user = $this->users->login(
                $params['login'],
                $params['password']
            );
            $this->lobby->addRacer($user->id);
            return $user;
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
        $this->lobby->removeRacer($params['token']);
        return $this->users->logout($params['token']);
    }

    public function checklog($params)
    {
        return $this->users->checklog($params['login']);
    }

    public function checkCookie()
    {
        if ($_COOKIE) {
            $token = $_COOKIE['token'];
            return $this->users->getUserByToken($token);
        }
    }

    /****************/
    /*****racing*****/
    /****************/
    public function checkStatus($params)
    {
        $arrivalId = $params['arrivalId'];
        return $this->lobby->checkStatus($arrivalId);
    }

    public function getRaces($params)
    {
        if ($this->getUser($params)) {
            return $this->lobby->getRaces();
        }
    }

    public function getAllRooms($params)
    {
        if ($this->getUser($params)) {
            return $this->lobby->getAllArrivals();
        }
    }

    public function joinArrival($params)
    {
        if ($this->getUser($params) && $params['arrivalId']) {
            return $this->arrival->joinArrival($params['token'], $params['arrivalId']);
        }
    }

    public function leaveArrival($params)
    {
        $user = $this->getUser($params);
        if ($user) {
            //echo "it's Britney, bitch";
            $roomId = $params['id'];
            return $this->arrival->leaveArrival($params['token'], $roomId);
        }
    }

    public function addArrival($params)
    {
        $user = $this->getUser($params);
        if ($user && $params['raceId']) {
            $arrival = $this->arrival->addArrival(
                $params['name'],
                $params['raceId']
            );
        }
        $this->arrival->joinArrival($params['token'], $arrival->id);
    }

    public function isArrivalReady()
    {
        return $this->arrival->isArrivalReady();
    }

    //UPDATE:
    public function getRacers($params)
    {
        $arrivalId = $params['arrivalId'];
        return $this->arrival->getRacers($arrivalId);
    }

    public function getСoordinates($params)
    {
        return $this->arrival->getСoordinates($params);
    }

    /* public function isArrivalReady($params) {
        return $this->arrival->isArrivalReady($params['arrivalId']);
    } */
}
