<?php
class Lobby
{
    function __construct($db)
    {
        $this->db = $db;
    }


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
    public function checkStatus($arrivalId)
    {
        $status = $this->db->checkStatus($arrivalId);
        if ($status)
            return $status;
    }
    public function getAllArrivals()
    {
        $arrivals = $this->db->getAllArrivals();
        //echo count($arrivals);
        $result = array();
        for ($i = 0; $i < count($arrivals); $i++) {
            $result[$i] = array(
                'id' => $arrivals[$i]->id,
                'name' => $arrivals[$i]->name,
                'status' => $arrivals[$i]->status,
                //'status' => $arrivals[$i]->status,
                //...
            );
        }
        return $result;
    }

    /****************/
    /*****racers*****/
    /****************/

    public function addRacer($userId)
    {
        $this->db->addRacer($userId);
    }

    public function removeRacer($token)
    {
        //$this->db->removeRacerFromArrival();
        $this->db->removeRacer($token);
    }
}
