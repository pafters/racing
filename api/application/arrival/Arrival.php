<?php
class Arrival {
    function __construct($db) {
        $this->db = $db;
    }

    public function getRaces() {
        return $this->db->getRaces();
    }

    public function setRacer($userId) {
        $racer = $this->db->getRacer($userId);
        if (!$racer) {
            // создать новую запись
            $this->db->createRacer($userId);
        }
    }
}