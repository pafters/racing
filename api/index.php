<?php

error_reporting(-1);
require_once('application/Application.php');

function router($params)
{
    $method = $params['method'];
    try {
        if ($method) {
            $app = new Application();
            switch ($method) {
                    //users
                case 'login':                               //работает, сразу добавляется гонщик в таблицу racer
                    return $app->login($params);
                case 'signup':
                    return $app->signup($params);
                case 'logout':
                    return $app->logout($params);           //работает, racer сразу удаляется
                case 'checklog':
                    return $app->checklog($params);
                case 'checkCookie':
                    return $app->checkCookie();

                    //racing
                    //case 'getRaces':
                    //    return $app->getRaces($params); //этот метод уже не нужен, но пусть пока побудет живым, вдруг что-то сдохнет
                case 'getAllRooms':
                    return $app->getAllRooms($params);
                case 'joinArrival':
                    return $app->joinArrival($params);      //в таблицу arrival в соотв строку добавляется racer с (!)нужным id - все ок
                case 'leaveArrival':
                    return $app->leaveArrival($params);
                case 'isArrivalReady':
                    return $app->isArrivalReady($params = 0);
                case 'createRoom':
                    return $app->addArrival($params);       //работает
                case 'checkStatus':
                    return $app->checkStatus($params);
                    //case 'checkStatus':
                    //    return $app->checkStatus();
                case 'getRace':
                    return true;
                case 'getRacers':
                    return $app->getRacers($params);
                case 'raceCommand':
                    return true;
                case 'getСoordinates':
                    return $app->getСoordinates($params);
            }
        }
        return false;
    } catch (Exception $e) {
        print_r($e->getMessage());
        //die();
        return false;
    }
}

function answer($data)
{
    if ($data) {
        return array(
            'data' => $data
        );
    }
    return array('data' => 'error');
}


echo json_encode(answer(router($_GET)));
