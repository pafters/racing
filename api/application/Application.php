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
        if ($params['racerId'] == 'no') {
            $racer = $this->getRacerByUserId();
            if ($racer) {
                //echo $racer->id;
                $racerId = $racer->id;
                return $this->arrival->getСoordinates($racerId);
            }
        } else {
            $racerId = $params['racerId'];
        }
        return $this->arrival->getСoordinates($racerId);
    }

    public function getRacerByUserId()
    {
        $user = $this->checkCookie();
        if ($user) {
            $userId = $user['id'];
            return $this->arrival->getRacerByUserId($userId);
            //if ($racer) {
            //    return $racer;
            //}
            //return $user;
        }
    }

    public function raceCommand($params)
    {
        $command = $params['command'];
        $w_height = $params['w_height'];
        $w_width = $params['w_width'];
        $racer = $this->getRacerByUserId();
        if ($racer) {
            $this->arrival->raceCommand($command, $racer->id, $w_height, $w_width);
        }
    }

    public function getAllCoordinates($params)
    {
        $racers = array($params['racer1'], $params['racer2'], $params['racer3'], $params['racer4']);
        $arrival_id = $params['arrival_id'];
        $w_height = $params['w_height'];
        $w_width = $params['w_width'];
        return $this->arrival->getAllCoordinates($racers, $arrival_id, $w_width, $w_height);
    }

    public function getBallByArrivalId($params)
    {
        $arrival_id = $params['arrival_id'];
        return $this->arrival->getBallByArrivalId($arrival_id);
    }

    public function ballMovement($params)
    {
        $arrival_id = $params['arrival_id'];
        $w_width = $params['w_width'];
        $w_height = $params['w_height'];
        return $this->arrival->ballMovement($arrival_id, $w_width, $w_height);
    }

    public function timer($params)
    {
        //set_time_limit(300);
        $arrival_id = $params['arrival_id'];
        $w_width = $params['w_width'];
        $this->arrival->changeLaserСoordinates($arrival_id, $w_width);
    }

    /* public function isArrivalReady($params) {
        return $this->arrival->isArrivalReady($params['arrivalId']);
    } */
}
