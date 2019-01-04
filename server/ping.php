<?php

require 'include/database_methods.php';

class Ping {
    private $user_login;
    private $device_ip;
    private $device_port;
    private $device_local_ip;
    private $device_local_port;
    private $error_log = array();

    function __construct() {
        $this->user_login = $_POST["user_login"];
        $this->device_ip = $_SERVER["REMOTE_ADDR"];
        $this->device_port = $_SERVER["REMOTE_PORT"];
        $this->device_local_ip = $_POST["local_ip"];
        $this->device_local_port = $_POST["local_port"];
    }

    public function ping() {
        $data_for_database = array(
            "user_login" => $this->user_login,
            "device_ip" => $this->device_ip,
            "device_port" => $this->device_port,
            "device_local_ip" => $this->device_local_ip,
            "device_local_port" => $this->device_local_port
        );

        if (!$this->post_to_db($data_for_database)) {
            $this->error("Database Error");
            return false;
        }
        return true;
    }

    public function get_error_log() {
        return $this->error_log;
    }

    private function error($err) {
        array_push($this->error_log, $err);
    }

    private function post_to_db($data) {
        $db = new Database();
        if ($db->connect_to_db()) {
            if ($db->post($data, "devices")) {
                return true;
            }
        }
        $this->error($db->get_error_log());
        return false;
    }
    
}

$ping = new Ping();
if (!$ping->ping()) {
    header('Content-Type: application/json');
    echo json_encode($ping->get_error_log());
}


header('Content-Type: application/json');
echo json_encode("ok");