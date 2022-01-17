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
        $racer = $this->getRacerByUserId($user->id);

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
                        $this->db->insert_Ball_Into_Arrival($arrivalId);
                        $this->db->insertPlayer_Killer_Into_Arrival($arrivalId);
                        $racers = array(
                            $upd_arrival->racer_1,
                            $upd_arrival->racer_2,
                            $upd_arrival->racer_3,
                            $upd_arrival->racer_4
                        );
                        $x = array(500, 650, 800, 950);
                        $y = 120;
                        $this->db->setStartСoordinates($racers, $x, $y);
                        return $arrival;
                    }
                    return $upd_arrival;
                }
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
                if ($arrival->status == 'racing') {
                    $this->db->setСoordinatesByRacerId($racer->id, -200, -200, 0, 0, $racer->coin);
                }
                $num = 0;
                if ($arrival->racer_1 == $user->id)
                    $num = 1;
                else if ($arrival->racer_2 == $user->id)
                    $num = 2;
                else if ($arrival->racer_3 == $user->id)
                    $num = 3;
                else if ($arrival->racer_4 == $user->id)
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
    /* public function getRacers($arrivalId)
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
                $racer = $this->db->getRacerByUserId($users[$num]);
                if ($racer)
                    $racers[$num] = $racer->id;
            }
        }
        return $racers;
    } */
    public function getRacers($arrivalId)       //не работает, потому что в бд arrival записываются userId, а не racerId
    {
        $arrival = $this->db->getArrivalById($arrivalId);
        if ($arrival) {
            $racerIDs = array(
                $arrival->racer_1,
                $arrival->racer_2,
                $arrival->racer_3,
                $arrival->racer_4,
            );
            //echo $racerIDs[0];
            $racers = array();
            for ($i = 0; $i < count($racerIDs); $i++) {
                $racer = $this->db->getRacerById($racerIDs[$i]);
                if ($racer)
                    $racers[$i] = $racer;
            }
        }
        return $racers;
    }

    public function getRacerById($racerId)
    {
        $coordinates = $this->db->getRacerById($racerId);
        if ($coordinates)
            return $coordinates;
    }

    public function getRacerByUserId($userId)
    {
        return $this->db->getRacerByUserId($userId);
    }

    public function getAllCoordinates($racerIDs, $arrival_id, $w_width, $w_height)
    { //тут много чего намудрено, но оно работает
        $dead = 0;
        for ($num = 0; $num < count($racerIDs); $num++) {
            $racers[$num] = $this->db->getRacerById($racerIDs[$num]);
            if ($racers[$num]->life == 0) {
                $dead++;
            }
        }
        if ($dead != 3 || $dead != 4) {
            $racers = array();
            $racers[count($racerIDs)] = $this->ballMovement($arrival_id, $w_width, $w_height);

            $racers[count($racerIDs) + 1] = $this->playerKillerMovement($arrival_id, $w_width, $w_height);

            for ($num = 0; $num < count($racerIDs); $num++) {
                $racers[$num] = $this->db->getRacerById($racerIDs[$num]);

                if (sqrt(($racers[$num]->x - $racers[count($racerIDs)]->x) ** 2 + ($racers[$num]->y - $racers[count($racerIDs)]->y) ** 2) <= 35) {
                    $ball = $this->db->getBallByArrivalId($arrival_id);
                    if ($ball) {
                        $this->db->setBallCoordinates(rand(40, $w_width), rand(40, $w_height), 3, 3, $ball->id);
                    }
                    $coin =  $racers[$num]->coin + 1;
                    $this->db->setСoordinatesByRacerId($racers[$num]->id, $racers[$num]->x, $racers[$num]->y, $racers[$num]->angle, $racers[$num]->life, $coin);
                }

                if (sqrt(($racers[$num]->x - $racers[count($racerIDs) + 1]->x) ** 2 + ($racers[$num]->y - $racers[count($racerIDs) + 1]->y) ** 2) <= 40) {
                    $this->db->setСoordinatesByRacerId($racers[$num]->id, -200, -200, 0, 0, $racers[$num]->coin);
                }
            }
        }
        //$dead = 0;
        if ($dead == 3) {
            $racers[count($racerIDs) + 2] = true;
            $this->db->setStatusOfArrival($arrival_id, 'ending');
            $this->db->delete_Player_Killer($racers[count($racerIDs) + 1]->id);
            $this->db->delete_Ball($racers[count($racerIDs)]->id);
            //sleep(10);
            $this->db->setStatusOfArrival($arrival_id, 'open');
            for ($num = 1; $num <= 4; $num++) {
                $this->db->removeRacerFromArrival($arrival_id, $num);
                //$this->db->setСoordinatesByRacerId($coordinates[$num]->id, -200, -200, 0, 0, $coordinates[$num]->coin);
            }
        }

        return $racers;
    }

    public function ballMovement($arrival_id, $w_width, $w_height)
    {
        $ball = $this->db->getBallByArrivalId($arrival_id);
        //echo $ball;
        if ($ball) {
            $speed_y = $ball->speed_y;
            $speed_x = $ball->speed_x;
            $x = $ball->x + $speed_x;
            $y = $ball->y + $speed_y;

            // right 
            if ($ball->x >= $w_width) {
                $x = $w_width - 10;

                $speed_x = -$speed_x;
            }

            // left
            if ($ball->x <= 0) {
                $x  += 10;
                $try = rand(0, 1);

                $speed_x = -$speed_x;
            }

            // down
            if ($ball->y >= $w_height) {
                $y = $w_height - 10;
                $speed_y = -$speed_y;
            }

            // up
            if ($ball->y <= 0) {
                $y += 10;

                $speed_y = -$speed_y;
            }
        }

        $answer = $this->db->setBallCoordinates($x, $y, $speed_y, $speed_x, $ball->id);
        if ($answer)
            return $this->db->getBallByArrivalId($arrival_id);
    }

    public function playerKillerMovement($arrival_id, $w_width, $w_height)
    {
        $player_killer = $this->db->get_Player_Killer_By_ArrivalId($arrival_id);
        if ($player_killer) {
            $speed_y = $player_killer->speed_y;
            $speed_x = $player_killer->speed_x;
            $x = $player_killer->x - $speed_x;
            $y = $player_killer->y - $speed_y;

            // right 
            if ($player_killer->x >= $w_width) {
                $x = $w_width - 10;
                $try = rand(0, 1);
                if ($try == 0) {
                    $speed_y = $speed_y;
                    $speed_x = -$speed_x;
                } else {
                    $speed_x = -$speed_x;
                    $speed_y = $speed_y;
                }
            }

            // left
            if ($player_killer->x <= 0) {
                $x  += 10;
                $try = rand(0, 1);
                if ($try == 0) {
                    $speed_y = $speed_y;
                    $speed_x = -$speed_x;
                } else {
                    $speed_x = $speed_x;
                    $speed_x = -$speed_y;
                }
            }

            // down
            if ($player_killer->y >= $w_height) {
                $y = $w_height - 10;
                $try = rand(0, 1);
                if ($try == 0) {
                    $speed_x = $speed_x;
                    $speed_y = -$speed_y;
                } else {
                    $speed_y = -$speed_y;
                    $speed_x = -$speed_x;
                }
            }

            // up
            if ($player_killer->y <= 0) {
                $y += 10;
                $try = rand(0, 1);
                if ($try == 0) {
                    $speed_x = -$speed_x;
                    $speed_y = -$speed_y;
                } else {
                    $speed_y = -$speed_y;
                    $speed_x = $speed_x;
                }
            }
        }
        $answer = $this->db->set_Player_Killer_Coordinates($x, $y, $speed_y, $speed_x, $player_killer->id);
        if ($answer)
            return $this->db->get_Player_Killer_By_ArrivalId($arrival_id);
    }

    public function getBallByArrivalId($arrival_id)
    {
        return $this->db->getBallByArrivalId($arrival_id);
    }

    public function get_Player_Killer_By_ArrivalId($arrival_id)
    {
        return $this->db->get_Player_Killer_By_ArrivalId($arrival_id);
    }

    public function raceCommand($command, $racerId, $w_height, $w_width)
    {
        $coordinates = $this->db->getRacerById($racerId);
        if ($coordinates) {
            $coin = $coordinates->coin;
            $life = $coordinates->life;
            if ($life == 1) {
                switch ($command) {
                    case 'W':
                        $boolean = true;
                        $count = 3;
                        $angle = $coordinates->angle;
                        while ($boolean) {
                            $x = $coordinates->x + $count * (4 * sin(M_PI / 180 * $angle) / 6);
                            $y = $coordinates->y - $count * (4 * cos(M_PI / 180 * $angle) / 6);
                            $count += 1;
                            if ($count > 6) {
                                $boolean = false;
                            }
                            $this->db->setСoordinatesByRacerId($racerId, $x, $y, $angle, $life, $coin);
                            sleep(1 / 80);
                        }

                        if ($coordinates->x < 10) {
                            $x = $coordinates->x + 10;
                            $this->db->setСoordinatesByRacerId($racerId, $x, $y, $angle, $life, $coin);
                        }
                        if ($coordinates->y < 10) {
                            $y = $coordinates->y + 10;
                            $this->db->setСoordinatesByRacerId($racerId, $x, $y, $angle, $life, $coin);
                        }
                        if ($coordinates->y > $w_height - 10) {
                            $y = $coordinates->y - 10;
                            $this->db->setСoordinatesByRacerId($racerId, $x, $y, $angle, $life, $coin);
                        }
                        if ($coordinates->x > $w_width - 10) {
                            $x = $coordinates->x - 10;
                            $this->db->setСoordinatesByRacerId($racerId, $x, $y, $angle, $life, $coin);
                        }

                        break;
                    case 'A':

                        $y = $coordinates->y;
                        $x = ($coordinates->x);

                        $angle = $coordinates->angle - 1.5;
                        if (abs($coordinates->angle) > 360) {
                            $angle = 0;
                        }
                        $this->db->setСoordinatesByRacerId($racerId, $x, $y, $angle, $life, $coin);
                        break;

                    case 'S':
                        $angle = $coordinates->angle;
                        $boolean = true;
                        $count = 1;
                        $angle = $coordinates->angle;
                        while ($boolean) {
                            $x = $coordinates->x - $count * (4 * sin(M_PI / 180 * $angle) / 6);
                            $y = $coordinates->y + $count *  (4 * cos(M_PI / 180 * $angle) / 6);
                            $count += 1;
                            if ($count > 6) {
                                $boolean = false;
                            }
                            $this->db->setСoordinatesByRacerId($racerId, $x, $y, $angle, $life, $coin);
                            sleep(1 / 30);
                        }
                        if ($coordinates->x < 10) {
                            $x = $coordinates->x + 10;
                        }
                        if ($coordinates->y < 10) {
                            $y = $coordinates->y + 10;
                        }
                        if ($coordinates->y > $w_height - 10) {
                            $y = $coordinates->y - 10;
                        }
                        if ($coordinates->x > $w_width - 10) {
                            $x = $coordinates->x - 10;
                        }
                        $this->db->setСoordinatesByRacerId($racerId, $x, $y, $angle, $life, $coin);
                        break;
                    case 'D':
                        $y = $coordinates->y;
                        $x = ($coordinates->x);
                        $angle = $coordinates->angle + 2.5;

                        if (abs($coordinates->angle) > 360) {
                            $angle = 0;
                        }
                        $this->db->setСoordinatesByRacerId($racerId, $x, $y, $angle, $life, $coin);
                        break;
                }
            }
        }
    }
}
