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
                        $this->db->insertBallCoordinates($arrivalId);
                        $this->db->insertLaserCoordinates($arrivalId);
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
                            //echo $racers[$num];
                        }
                        $x = array(200, 350, 500, 650);
                        $y = 200;
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
                $racer = $this->db->getRacerByUserId($users[$num]);
                if ($racer)
                    $racers[$num] = $racer->id;
            }
        }
        return $racers;
    }

    public function getСoordinates($racerId)
    {
        $coordinates = $this->db->getСoordinates($racerId);
        if ($coordinates)
            return $coordinates;
    }

    public function getRacerByUserId($userId)
    {
        return $this->db->getRacerByUserId($userId);
    }

    public function getAllCoordinates($racers, $arrival_id, $w_width, $w_height)
    {
        $begin = true;
        $coordinates = array();
        $coordinates[count($racers)] = $this->ballMovement($arrival_id, $w_width, $w_height);
        //if ($begin == true) {
        $laser = $this->db->getLaserByArrivalId($arrival_id);
        $coordinates[count($racers) + 1] = $laser;
        //}
        for ($num = 0; $num < count($racers); $num++) {
            $coordinates[$num] = $this->db->getСoordinates($racers[$num]);

            if (sqrt(($coordinates[$num]->x - $coordinates[count($racers)]->x) ** 2 + ($coordinates[$num]->y - $coordinates[count($racers)]->y) ** 2) <= 30) {
                $ball = $this->db->getBallByArrivalId($arrival_id);
                if ($ball) {
                    $this->db->setBallCoordinates(rand(40, $w_width), rand(40, $w_height), 3, 3, $ball->id);
                }
                $coin =  $coordinates[$num]->coin + 1;
                $this->db->setСoordinatesByRacerId($coordinates[$num]->id, $coordinates[$num]->x, $coordinates[$num]->y, $coordinates[$num]->angle, $coordinates[$num]->speed, $coordinates[$num]->life, $coin);
            }
            for ($i = 0; $i < $w_height; $i++) {
                if (sqrt(($coordinates[$num]->x - $coordinates[count($racers) + 1]->x2) ** 2 + ($coordinates[$num]->y - $i) ** 2) <= 30) {
                    $this->db->setСoordinatesByRacerId($coordinates[$num]->id, -200, -200, 0, 0, 0, $coordinates[$num]->coin);
                }
            }
            //$laser = $this->changeLaserСoordinates($arrival_id, $w_width, $w_height);
            //if (sqrt(($coordinates[$num]->x - $coordinates[count($racers)]->x) ** 2 + ($coordinates[$num]->y - $coordinates[count($racers)]->y) ** 2) <= 20) {
            //    $this->db->setСoordinatesByRacerId($coordinates[$num]->id, -200, -200, 0, 0, 0, $coordinates[$num]->coin);
            //}
        }
        if (($coordinates[0]->life && $coordinates[1]->life && $coordinates[2]->life &&  $coordinates[3]->life) == 0) {
            $begin = false;
        }

        return $coordinates;
    }

    public function changeLaserСoordinates($arrival_id, $w_width)
    {
        $laser = $this->db->getLaserByArrivalId($arrival_id);
        while (true) {
            if ($laser) {
                $x2 = -10;
                $x = rand(10, $w_width - 10);
                $this->db->setLaserСoordinates($laser->id, $x, $x2);
            }
            sleep(6);
            $x2 = $x;
            $this->db->setLaserСoordinates($laser->id, $x, $x2);
            sleep(10);
        }

        return $this->db->getLaserByArrivalId($arrival_id);
    }

    public function ballMovement($arrival_id, $w_width, $w_height)
    {
        $ball = $this->db->getBallByArrivalId($arrival_id);
        if ($ball) {
            $speed_y = $ball->speed_y;
            $speed_x = $ball->speed_x;
            $x = $ball->x + $speed_x;
            $y = $ball->y + $speed_y;

            // right 
            if ($ball->x >= $w_width) {
                $x = $w_width - 10;
                $try = rand(0, 1);
                if ($try == 0) {
                    $speed_x = $speed_x;
                } else {
                    $speed_x = -$speed_x;
                }
            }

            // left
            if ($ball->x <= 0) {
                $x  += 10;
                $try = rand(0, 1);
                if ($try == 0) {
                    $speed_x = $speed_x;
                } else {
                    $speed_x = -$speed_x;
                }
            }

            // down
            if ($ball->y >= $w_height) {
                $y = $w_height - 10;
                $try = rand(0, 1);
                if ($try == 0) {
                    $speed_y = $speed_y;
                } else {
                    $speed_y = -$speed_y;
                }
            }

            // up
            if ($ball->y <= 0) {
                $y += 10;
                $try = rand(0, 1);
                if ($try == 0) {
                    $speed_y = $speed_y;
                } else {
                    $speed_y = -$speed_y;
                }
            }
        }

        $answer = $this->db->setBallCoordinates($x, $y, $speed_y, $speed_x, $ball->id);
        if ($answer)
            return $this->db->getBallByArrivalId($arrival_id);
    }

    public function getBallByArrivalId($arrival_id)
    {
        return $this->db->getBallByArrivalId($arrival_id);
    }

    public function raceCommand($command, $racerId, $w_height, $w_width)
    {
        $coordinates = $this->db->getСoordinates($racerId);
        if ($coordinates) {
            $speed = $coordinates->speed;
            $life = $coordinates->life;
            $coin = $coordinates->coin;
            if ($life == 1) {
                if (!$speed) $speed = 0;
                switch ($command) {
                    case 'W':
                        $speed += 1; //временно не используется
                        if ($speed > 6) {
                            $speed = 6;
                        }
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
                            $this->db->setСoordinatesByRacerId($racerId, $x, $y, $angle, $speed, $life, $coin);
                            sleep(1 / 80);
                        }

                        if ($coordinates->x < 10) {
                            $x = $coordinates->x + 10;
                            $this->db->setСoordinatesByRacerId($racerId, $x, $y, $angle, $speed, $life, $coin);
                        }
                        if ($coordinates->y < 10) {
                            $y = $coordinates->y + 10;
                            $this->db->setСoordinatesByRacerId($racerId, $x, $y, $angle, $speed, $life, $coin);
                        }
                        if ($coordinates->y > $w_height - 10) {
                            $y = $coordinates->y - 10;
                            $this->db->setСoordinatesByRacerId($racerId, $x, $y, $angle, $speed, $life, $coin);
                        }
                        if ($coordinates->x > $w_width - 10) {
                            $x = $coordinates->x - 10;
                            $this->db->setСoordinatesByRacerId($racerId, $x, $y, $angle, $speed, $life, $coin);
                        }

                        break;
                    case 'A':
                        //$speed--;
                        $y = $coordinates->y;
                        $x = ($coordinates->x);

                        $angle = $coordinates->angle - 1.5;
                        if (abs($coordinates->angle) > 360) {
                            $angle = 0;
                        }
                        $this->db->setСoordinatesByRacerId($racerId, $x, $y, $angle, $speed, $life, $coin);
                        break;

                    case 'S':
                        $speed -= 1;
                        if ($speed < -6) {
                            $speed = -6;
                        }

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
                            $this->db->setСoordinatesByRacerId($racerId, $x, $y, $angle, $speed, $life, $coin);
                            sleep(1 / 30);
                        }
                        //$x = $coordinates->x + $speed * sin(M_PI / 180 * $angle);
                        //$y = $coordinates->y - $speed * cos(M_PI / 180 * $angle);
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
                        $this->db->setСoordinatesByRacerId($racerId, $x, $y, $angle, $speed, $life, $coin);
                        break;
                    case 'D':
                        //$speed--;
                        $y = $coordinates->y;
                        $x = ($coordinates->x);
                        $angle = $coordinates->angle + 2.5;

                        if (abs($coordinates->angle) > 360) {
                            $angle = 0;
                        }
                        $this->db->setСoordinatesByRacerId($racerId, $x, $y, $angle, $speed, $life, $coin);
                        break;
                }
            }
        }
    }
}
