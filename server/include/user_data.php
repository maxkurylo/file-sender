<?php

require_once "database_methods.php";

abstract class User {
    protected $user_login;
    protected $user_password;
    protected $is_user_data_valid = false;
    protected $error_log = array();

    //public methods

    public function get_error_log() {
        return $this->error_log;
    }

    public function validate_user_data() {
        if ($this->is_login_or_password_empty()) {
            return false;
        }

        if (!$this->is_user_login_valid()) {
            return false;
        }

        if (!$this->is_user_password_valid()) {
            return false;
        }

        $this->is_user_data_valid = true;
        return true;
    }

    //protected methods

    protected function error($err) {
        array_push($this->error_log, $err);
    }

    protected function is_login_or_password_empty() {
        if(!$this->user_login || !$this->user_password) {
            return true;
        }
        return false;
    }

    protected function is_user_login_valid() {
        if (!filter_var($this->user_login, FILTER_VALIDATE_EMAIL)) {
            $this->error("User login error: invalid user name");
            return false;
        }
        return true;
    }

    protected function is_user_password_valid() {
        if (strlen($this->user_password) < 8) {
            $this->error("User password error: password should be at least 8 digits long");
            return false;
        }
        elseif (strlen($this->user_password) > 35) {
            $this->error("User password error: password should be no more than 35 digits long");
            return false;
        }
        return true;
    }
}


class SignInUser extends User {
    private $user_login_from_db;
    private $user_password_hash_from_db;
    private $user_exists = true;

    //public methods

    function __construct($user_login, $user_password) {
        $this->user_login = $user_login;
        $this->user_password = $user_password;
    }

    public function sign_in_user() {
        if(!$this->is_user_data_valid) {
            $this->error("User data error: user login or password is invalid");
            return false;
        }

        if(!$this->get_login_and_password_from_db()) {
            return false;
        }

        if( !$this->verify_password() || !$this->verify_login() ) {
            return false;
        }

        return true;
    }

    //private methods

    private function verify_password() {
        $this->error("User data error: password is incorrect");
        return password_verify($this->user_password, $this->user_password_hash_from_db);
    }

    private function verify_login() {
        $this->error("User data error: login or password is incorrect");
        return $this->user_login == $this->user_login_from_db;
    }

    private function get_login_and_password_from_db() {
        $user_data = $this->get_from_db();
        if (empty($user_data)) {
            $this->user_exists = false;
            return false;
        }

        if (!$user_data[0]["user_login"] || !$user_data[0]["user_password_hash"]) {
            $this->error("Database error");
            return false;
        }

        $this->user_login_from_db = $user_data[0]["user_login"];
        $this->user_password_hash_from_db = $user_data[0]["user_password_hash"];
        return true;
    }

    private function get_from_db() {
        $bad_response = array(false);
        $user_data = array();
        $db = new Database();

        if ($db->connect_to_db()) {
            $user_data = $db->get($this->user_login);
            if (!empty($user_data)) {
                if ($user_data[0] === false) {
                    $this->error($db->get_error_log());
                    return $bad_response;
                }
            }
        }
        else {
            $this->error($db->get_error_log());
            return $bad_response;
        }

        return $user_data;
    }

    //getters

    public function get_user_login() {
        return $this->user_login;
    }

    public function get_user_password() {
        return $this->user_password;
    }

    public function does_user_exists() {
        return $this->user_exists;
    }

}



class RegisterUser extends User {
    private $user_password_hash;

    //public methods

    function __construct($user_login, $user_password) {
        $this->user_login = $user_login;
        $this->user_password = $user_password;
    }

    public function create_user() {
        if(!$this->is_user_data_valid) {
            $this->error("User data error: user login or password is invalid");
            return false;
        }

        $this->user_password_hash = $this->hash_password();

        $data_for_db = array(
            "user_login" => $this->user_login,
            "user_password_hash" => $this->user_password_hash
        );

        return $this->post_to_db($data_for_db);
    }

    //private methods

    private function hash_password() {
        return password_hash($this->user_password, PASSWORD_DEFAULT);
    }

    private function post_to_db($data) {
        $db = new Database();
        if ($db->connect_to_db()) {
            if ($db->post($data)) {
                return true;
            }
        }

        $this->error($db->get_error_log());
        return false;
    }
}
?>