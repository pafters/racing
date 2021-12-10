<?php

require_once('db/DB.php');
require_once('users/Users.php');
require_once('arrival/Arrival.php');
require_once('racing/Racing.php');

class Application
{
    function __construct()
    {
        $db = new DB();
        $this->users = new Users($db);
        //$this->arrival = new Arrival($db);
        $this->racing = new Racing($db);
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
        $this->racing->removeRacer($params['token']);
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

    public function getRaces($params)
    {
        if ($params['token']) {
            if ($this->users->getUserByToken($params['token'])) {
                return $this->racing->getRaces();
            }
        }
    }

    public function getAllRooms($params)
    {
        if ($params['token']) {
            if ($this->users->getUserByToken($params['token'])) {
                return $this->racing->getAllArrivals();
            }
        }
    }

    public function joinArrival($params)
    {
        if ($params['token']) {
            if ($this->users->getUserByToken($params['token'])) {
                $raceId = ($params['raceId']) ? $params['raceId'] : 1;
                return $this->racing->joinArrival($params['token'], $raceId);
            }
        }
    }

    /* public function createRoom($params) {
        if ($params['token']) {
            if ($this->users->getUserByToken($params['token'])) {
                return $this->racing->addArrival($params['token'], $params['name'], $params['raceId']);
            }
        }
    } */

    public function addArrival($params)
    {
        if ($params['token']) {
            if ($this->users->getUserByToken($params['token'])) {
                if ($params['raceId'])
                    $this->racing->addArrival($params['token'], $params['name'], $params['raceId']);
                else
                    $this->racing->addArrival($params['token'], $params['name'], 1);
            }
        }
    }

    public function isArrivalReady()
    {
        $this->db->isArrivalReady();
    }
}
