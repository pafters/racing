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
        $stmt = $this->db->query($query);
        $result = array();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $result[] = $row;
        }
        return $result;
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
        $this->db->query("INSERT INTO `racer` (`id`, `user_id`, `x`, `y`, `angle`, `speed`) VALUES (NULL, '" . $id . "', NULL, NULL, NULL, NULL);");
    }

    public function getRacerByUserId($userId)
    {
        $racer = $this->db->query("SELECT * FROM racer WHERE `user_id` = '" . $userId . "'")->fetchObject();
        return $racer;
    }

    public function removeRacer($token)
    {
        $user = $this->getUserByToken($token);
        $racer = $this->getRacerByUserId($user->id);
        $this->db->query("DELETE FROM `racer` WHERE `racer`.`id` = ".$racer->id);
    }

    /***************/
    /*****races*****/
    /***************/

    public function getRaces()
    {
        $query = "SELECT `name` FROM race";
        $stmt = $this->db->query($query);
        $result = array();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $result[] = $row;
        }
        return $result;
    }

    /****************/
    /****arrivals****/
    /****************/

    public function getAllArrivals() {
        $query = "SELECT * FROM arrival";
        $stmt = $this->db->query($query);
        $result = array();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $result[] = $row;
        }
        return $result;
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

    public function setStatusOfArrival($arrivalId, $status)
    {
        $this->db->query("UPDATE `arrival` SET `status` = " . $status . " WHERE `arrival`.`id` = " . $arrivalId . ";");
    }
}
