<?php
class DB
{
    function __construct()
    {
        $host = 'localhost';
        $port = '3306';
        $name = 'racing';
        $user = 'root';
        $password = '';

        try {
            $this->db = new PDO(
                'mysql:' .
                    'host=' . $host . ';' .
                    'port=' . $port . ';' .
                    'dbname=' . $name,
                $user,
                $password
            );
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            print_r($e->getMessage());
            die();
        }
    }

    function __destruct()
    {
        $this->db = null;
    }

    private function getArray($query)
    {
        $stmt = $this->db->query($query);
        $result = array();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $result[] = $row;
        }
        return $result;
    }

    /***************/
    /*****users*****/
    /***************/

    public function addUser($name, $login, $password)
    {
        $query = "INSERT INTO `users` (`id`, `name`, `login`, `password`, `token`) VALUES (NULL, '" . $name . "', '" . $login . "', '" . $password . "', NULL)";
        $this->db->query($query);
    }

    public function getUser($login)
    {
        $query = 'SELECT * FROM users WHERE login = "' . $login . '"';
        return $this->db->query($query)->fetchObject();
    }

    public function getUsers()
    {
        $query = 'SELECT * FROM users';
        return $this->getArray($query);
    }

    /*****token*****/

    public function getUserByToken($token)
    {
        $query = 'SELECT * FROM users WHERE token = "' . $token . '"';
        return $this->db->query($query)->fetchObject();
    }
    public function addToken($id)
    {
        $token = md5(rand());
        $query = "UPDATE users SET token='" . $token . "' WHERE id=" . $id;
        $this->db->query($query);
        return $token;
    }

    public function removeToken($token)
    {
        $query = "UPDATE `users` SET `token` = NULL WHERE token LIKE '" . $token . "'";
        $this->db->query($query);
    }

    public function getToken($login)
    {
        $query = "SELECT `token` FROM users WHERE login = '" . $login . "' ";
        return $this->db->query($query)->fetchObject();
    }

    /****************/
    /*****racers*****/
    /****************/

    public function addRacer($id)
    {
        $this->db->query("INSERT INTO `racer` (`id`, `user_id`, `x`, `y`, `angle`, `coin`) VALUES (NULL, " . $id . ", NULL, NULL, NULL, NULL);");
        return true;
    }

    public function getRacerByUserId($userId)
    {
        return $this->db->query("SELECT * FROM racer WHERE `user_id` = '" . $userId . "'")->fetchObject();
    }

    public function getRacerById($racerId)
    {
        return $this->db->query("SELECT * FROM `racer` WHERE `id` = " . $racerId . ";")->fetchObject();
    }

    public function removeRacer($token)
    {
        $user = $this->getUserByToken($token);
        if ($user)
            $racer = $this->getRacerByUserId($user->id);
        if ($racer)
            $this->db->query("DELETE FROM `racer` WHERE `racer`.`id` = " . $racer->id);
    }

    /***************/
    /*****races*****/
    /***************/

    public function getRaces()
    {
        $query = "SELECT `name` FROM race";
        return $this->getArray($query);
    }

    /****************/
    /****arrivals****/
    /****************/

    public function getAllArrivals()
    {
        $query = "SELECT * FROM arrival";
        return $this->getArray($query);
    }

    public function checkStatus($arrivalId)
    {
        $status = $this->db->query("SELECT `status` FROM `arrival` WHERE `id` = '" . $arrivalId . "'")->fetchObject();
        return $status;
    }

    public function getAllOpenArrivals()
    {
        $query = "SELECT * FROM arrival WHERE `status` = 'open'";
        return $this->getArray($query);
    }

    public function addArrival($name, $raceId)
    {
        $this->db->query("INSERT INTO `arrival` (`id`, `name`, `race_id`, `status`, `racer_1`, `racer_2`, `racer_3`, `racer_4`) VALUES (NULL, '" . $name . "', '" . $raceId . "', '', NULL, NULL, NULL, NULL);");
        return $this->db->query("SELECT * FROM arrival WHERE `name` = '" . $name . "'")->fetchObject();
    }

    public function getArrivalById($id)
    {
        $arrival = $this->db->query("SELECT * FROM `arrival` WHERE `id` = '" . $id . "'")->fetchObject();
        return $arrival;
    }

    public function addRacerIntoArrival($arrivalId, $racerId, $racerNum)
    {
        $this->db->query("UPDATE `arrival` SET `racer_" . $racerNum . "` = '" . $racerId . "' WHERE `arrival`.`id` = " . $arrivalId . ";");
    }

    public function removeRacerFromArrival($arrivalId, $racerNum)
    {
        $this->db->query("UPDATE `arrival` SET `racer_" . $racerNum . "` = NULL WHERE `arrival`.`id` = " . $arrivalId . ";");
    }

    public function setStatusOfArrival($arrivalId, $status)
    {
        $this->db->query("UPDATE `arrival` SET `status` = '" . $status . "' WHERE `arrival`.`id` = '" . $arrivalId . "'");
    }

    /**************/
    /*****game*****/
    /**************/
    public function setStartСoordinates($racers, $x, $y)
    {
        for ($num = 0; $num < count($racers); $num++) {
            $this->db->query("UPDATE `racer` SET `x` = '" . $x[$num] . "', `y` = '" . $y . "', `life` = '1', `coin` = '0', `angle` = '0'  WHERE `id` = " . $racers[$num] . ";");
        }
    }

    public function delete_Player_Killer($player_killer_id)
    {
        $this->db->query("DELETE FROM `player_killer` WHERE `player_killer`.`id` = '" . $player_killer_id . "'");
    }

    public function delete_Ball($ball_id)
    {
        $this->db->query("DELETE FROM `ball` WHERE `ball`.`id` = '" . $ball_id . "'");
    }

    public function setСoordinatesByRacerId($racerId, $x, $y, $angle, $life, $coin)
    {
        $this->db->query("UPDATE `racer` SET `x` = '" . $x . "', `y` = '" . $y . "', `angle` = '" . $angle . "' , `life` = '" . $life . "' ,`coin` = '" . $coin . "'  WHERE `id` = " . $racerId . ";");
    }

    public function insert_Ball_Into_Arrival($arrival_id)
    {
        $this->db->query("INSERT INTO `ball` (`arrival_id`, `id`, `x`, `y`, `speed_y`, `speed_x`) VALUES ('" . $arrival_id . "', NULL ,'300', '300', '3' , '3');");
    }

    public function getBallByArrivalId($arrival_id)
    {
        $ball = $this->db->query("SELECT * FROM `ball` WHERE `arrival_id` = '" . $arrival_id . "'")->fetchObject();
        return $ball;
    }

    public function setBallCoordinates($x, $y, $speed_y, $speed_x, $ball_id)
    {
        $this->db->query("UPDATE `ball` SET `x` = '" . $x . "', `y` = '" . $y . "', `speed_y` = '" . $speed_y . "' , `speed_x` = '" . $speed_x . "'  WHERE `id` = '" . $ball_id . "'");
        return true;
    }

    public function insertPlayer_Killer_Into_Arrival($arrival_id)
    {
        $this->db->query("INSERT INTO `player_killer` (`arrival_id`, `id`, `x`, `y`, `speed_y`, `speed_x`) VALUES ('" . $arrival_id . "', NULL ,'300', '300', '5' , '5');");
    }

    public function get_Player_Killer_By_ArrivalId($arrival_id)
    {
        $player_killer = $this->db->query("SELECT * FROM `player_killer` WHERE `arrival_id` = '" . $arrival_id . "'")->fetchObject();
        return $player_killer;
    }

    public function set_Player_Killer_Coordinates($x, $y, $speed_y, $speed_x, $ball_id)
    {
        $this->db->query("UPDATE `player_killer` SET `x` = '" . $x . "', `y` = '" . $y . "', `speed_y` = '" . $speed_y . "' , `speed_x` = '" . $speed_x . "'  WHERE `id` = '" . $ball_id . "'");
        return true;
    }
}
