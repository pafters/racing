<?php
class Arrival
{

    function __construct($db)
    {
        $this->db = $db;
    }

    public function joinArrival($token, $arrivalId)
    {
        $user = $this->db->getUserByToken($token); //проблем не должно возникнуть
        $racer = $this->db->getRacerByUserId($user->id);

        if ($user) {
            $arrival = $this->db->getArrivalById($arrivalId); // должно нормально работать
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
            }
            if ($num != 0) {
                $this->db->addRacerIntoArrival($arrivalId, $racer->id, $num);
                $upd_arrival = $this->db->getArrivalById($arrivalId);
                if ($upd_arrival) {
                    if ($upd_arrival->racer_1 && $upd_arrival->racer_2 && $upd_arrival->racer_3 && $upd_arrival->racer_4) {
                        $this->db->setStatusOfArrival($arrivalId, 'racing');
                        $users = array(
                            $upd_arrival->racer_1,
                            $upd_arrival->racer_2,
                            $upd_arrival->racer_3,
                            $upd_arrival->racer_4
                        );
                        $racers = array();
                        for ($num = 0; $num < count($users); $num++) {

                            $racerInfo = $this->db->getRacerByUserId($users[$num]);
                            $racers[$num] = $racerInfo->id;
                            echo $racers[$num];
                        }
                        $this->db->setСoordinates($racers);
                        return $arrival;
                    }
                    return $upd_arrival;
                }
                //можно было это вот так сделать:
                /* if ($num == 4) {
                    $this->db->startRacing($arrivalId, $arrival->racer_1, $arrival->racer_2, $arrival->racer_3, $arrival->racer_4); // либо условие неправильное либо метод не работает
                    $this->db->setStatusOfArrival($arrivalId, 'racing');
                    return array(
                        'status' => 'racing',
                        'id' => array(
                            $arrival->racer_1,
                            $arrival->racer_2,
                            $arrival->racer_3,
                            $arrival->racer_4
                        )
                    );
                }
                return array(
                    'status' => 'open',
                    'id' => array(
                        $arrival->racer_1,
                        $arrival->racer_2,
                        $arrival->racer_3,
                        $arrival->racer_4
                    )
                ); */
                //но это же лишнее, у нас и так постоянно проверка на заполненность заезда происходит: isArrivalReady() с интервалом
            }
            return false;
        }
        return false;
    }

    public function leaveArrival($token, $arrivalId)
    {
        $user = $this->db->getUserByToken($token);

        if ($user) {
            $racer = $this->db->getRacerByUserId($user->id);
            $arrival = $this->db->getArrivalById($arrivalId);
            if ($arrival) {
                $num = 0;
                if ($arrival->racer_1 == $racer->id)
                    $num = 1;
                else if ($arrival->racer_2 == $racer->id)
                    $num = 2;
                else if ($arrival->racer_3 == $racer->id)
                    $num = 3;
                else if ($arrival->racer_4 == $racer->id)
                    $num = 4;
            }
            if ($num) {
                $this->db->removeRacerFromArrival($arrivalId, $num);
                return true;
            } else {
                return false;
            }
        }
    }

    public function addArrival($arrivalName, $raceId)
    {
        $arrival = $this->db->addArrival($arrivalName, $raceId);
        $this->db->setStatusOfArrival($arrival->id, 'open');
        return $arrival;
    }

    /* public function isArrivalReady($arrivalId)
    {
        $arrival = $this->db->getArrivalById($arrivalId);
        if ($arrival->racer_1)
            if ($arrival->racer_2)
                if ($arrival->racer_3)
                    if ($arrival->racer_4) {
                        $this->db->setStatusOfArrival($arrivalId, 'racing');
                        return true;
                    }
        return false;
    } */

    public function isArrivalReady()
    {
        $arrivals = $this->db->getAllOpenArrivals();
        for ($i = 0; $i < count($arrivals); $i++) {
            $arrival = $arrivals[$i];
            if ($arrival->racer_2)
                if ($arrival->racer_3)
                    if ($arrival->racer_4) {
                        $this->db->setStatusOfArrival($arrival->id, 'racing');
                        return true;
                    }
        }
        //return false;
        return  array(
            'status' => 'open'
        );
    }
    //UPDATE:
    public function getRacers($arrivalId)
    {
        $arrival = $this->db->getArrivalById($arrivalId);
        if ($arrival) {
            $users = array(
                $arrival->racer_1,
                $arrival->racer_2,
                $arrival->racer_3,
                $arrival->racer_4,
            );
            $racers = array();
            for ($num = 0; $num < count($users); $num++) {
                $num2 = $num + 1;
                $racers[$num] = $this->db->getRacerByUserId($users[$num]);
            }
        }
        return $racers;
    }

    public function getСoordinates($racers)
    {
        $coordinates = $this->db->getRacerByUserId($racers);
        if ($coordinates)
            return $coordinates;
    }
}
