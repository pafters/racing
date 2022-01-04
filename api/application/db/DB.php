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
        $this->db->query("INSERT INTO `racer` (`id`, `user_id`, `x`, `y`, `angle`, `speed`) VALUES (NULL, " . $id . ", NULL, NULL, NULL, NULL);");
        return true;
    }

    public function getRacerByUserId($userId)
    {
        $racer = $this->db->query("SELECT * FROM racer WHERE `user_id` = '" . $userId . "'")->fetchObject();
        return $racer;
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

    /* public function addArrival($name, $raceId)
    {
        $token = md5(rand());
        $arrival = $this->db->query("INSERT INTO `arrival` (`id`, `name`, `token`, `race_id`, `status`, `racer_1`, `racer_2`, `racer_3`, `racer_4`) VALUES (NULL, '" . $name . "', '" . $token . "', '" . $raceId . "', '', NULL, NULL, NULL, NULL);");
        if ($arrival) {
            $arrivalId = $this->db->query("SELECT `id` FROM `arrival` WHERE `token` = '" . $token . "'")->fetchObject();
            if ($arrivalId) {
                for ($num = 1; $num <= 4; $num++)
                    $this->db->query("INSERT INTO `racer` (`arrival_id`, `id`, `user_id`, `x`, `y`, `angle`, `speed`) VALUES ('" . $arrivalId->id . "', NULL, NULL, NULL, NULL, NULL, NULL);");
            }
            //чтобы добавить пользаков в гонку нужно добавить трассу в racer заранее
        }

        return $this->db->query("SELECT * FROM arrival WHERE `name` = '" . $name . "'")->fetchObject();
    } */

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

    public function startRacing($arrivalId, $racer_1, $racer_2, $racer_3, $racer_4)
    {
        $racerid = array($racer_1, $racer_2, $racer_3, $racer_4);

        $query = "SELECT * FROM `racer` WHERE `racer`.`arrival_id` = '" . $arrivalId . "'";
        $stmt = $this->db->query($query);
        $result = array();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $result[] = $row;
        }

        if ($result) {
            $races = array();
            for ($i = 0; $i < count($result); $i++) {
                $races[$i] = array(
                    'id' => $result[$i]->id,
                );
                $this->db->query("UPDATE `racer` SET `user_id` = '" . $racerid[$i] . "' , `life` = '1', `coin` = `1` WHERE `racer`.`arrival_id` = '" . $arrivalId . "' AND `racer`.`id` = '" . $races[$i]['id'] . "'");
            }
        }
    }

    //UPDATE:
    public function setStartСoordinates($racers, $x, $y)
    {
        for ($num = 0; $num < count($racers); $num++) {
            $this->db->query("UPDATE `racer` SET `x` = '" . $x[$num] . "', `y` = '" . $y . "'  WHERE `id` = " . $racers[$num] . ";");
        }
    }

    public function getСoordinates($racerId)
    {
        return $this->db->query("SELECT * FROM `racer` WHERE `id` = " . $racerId . ";")->fetchObject();
    }

    public function setStatusOfArrival($arrivalId, $status)
    {
        $this->db->query("UPDATE `arrival` SET `status` = '" . $status . "' WHERE `arrival`.`id` = '" . $arrivalId . "'");
    }

    public function setСoordinatesByRacerId($racerId, $x, $y, $angle, $speed, $life, $coin)
    {
        $this->db->query("UPDATE `racer` SET `x` = '" . $x . "', `y` = '" . $y . "', `angle` = '" . $angle . "' ,`speed` = '" . $speed . "',`life` = '" . $life . "' ,`coin` = '" . $coin . "'  WHERE `id` = " . $racerId . ";");
    }

    public function insertBallCoordinates($arrival_id)
    {
        $this->db->query("INSERT INTO `ball` (`arrival_id`, `id`, `x`, `y`, `speed_y`, `speed_x`) VALUES ('" . $arrival_id . "', NULL, ' 300 ', '300', '2','2');");
    }

    public function insertLaserCoordinates($arrival_id)
    {
        $this->db->query("INSERT INTO `laser` (`arrival_id`, `id`, `x`, `y`) VALUES ('" . $arrival_id . "', NULL, NULL, NULL);");
    }

    public function getLaserByArrivalId($arrival_id)
    {
        return $this->db->query("SELECT * FROM `laser` WHERE `arrival_id` = '" . $arrival_id . "';")->fetchObject();
    }

    public function setLaserСoordinates($id, $x, $x2)
    {
        $this->db->query("UPDATE `laser` SET `x` = '" . $x . "', `x2` = '" . $x2 . "' WHERE `id` = " . $id . ";");
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
    /*
    public function getBallCoordinates($ballId)
    {
        $ball = $this->db->query("SELECT * FROM `ball` WHERE `id` = '" . $ballId . "'")->fetchObject();
        return $ball;
    }*/
}
