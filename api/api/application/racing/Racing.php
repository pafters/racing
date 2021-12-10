<?php
class Racing
{

    function __construct($db)
    {
        $this->db = $db;
    }

    /****************/
    /*****racers*****/
    /****************/

    public function removeRacer($token)
    {
        $this->db->removeRacer($token);
    }

    /***************/
    /*****races*****/
    /***************/

    public function getRaces()
    {
        $races = $this->db->getRaces();
        $result = array();
        for ($i = 0; $i < count($races); $i++) {
            $result[$i] = array(
                'id' => $races[$i]->id,
                'name' => $races[$i]->name,
                //'data' => $races[$i]->data,
                //...
            );
        }
        return $result;
    }

    /****************/
    /****arrivals****/
    /****************/

    public function getAllArrivals()
    {
        $arrivals = $this->db->getAllArrivals();
        //echo count($arrivals);
        $result = array();
        for ($i = 0; $i < count($arrivals); $i++) {
            $result[$i] = array(
                'id' => $arrivals[$i]->id,
                'name' => $arrivals[$i]->name,
                //'race_id' => $arrivals[$i]->race_id,
                //...
            );
        }
        return $result;
    }

    public function joinArrival($token, $arrivalId)
    {
        $user = $this->db->getUserByToken($token);
        $racer = $this->db->getRacerByUserId($user->id);

        $arrival = $this->db->getArrivalById($arrivalId);

        $num = 0;

        if ($arrival) {

            if (!$arrival->racer_1)
                $num = 1;
            else if (!$arrival->racer_2)
                $num = 2;
            else if (!$arrival->racer_3)
                $num = 3;
            else if (!$arrival->racer_4)
                $num = 4;

            if ($num)
                //если нашлась свободная ячейка
                $this->db->addRacerIntoArrival($arrivalId, $racer->id, $num);
            else {
                //изменить статус заезда на racing
                return false;
            }
        } else return false;
    }

    public function addArrival($token, $name, $raceId)
    {
        $arrivalId = $this->db->addArrival($name, $raceId)->id;
        $this->joinArrival($token, $arrivalId);
    }

    public function isArrivalReady($id)
    {
        $arrival = $this->db->getArrivalById($id);
        if ($arrival->racer_1)
            if ($arrival->racer_2)
                if ($arrival->racer_3)
                    if ($arrival->racer_4) {
                        $this->db->setStatusOfArrival($id, 'racing');
                        return true;
                    }
        return false;
    }
}
