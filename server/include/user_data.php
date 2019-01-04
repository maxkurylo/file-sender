<?php

require_once "database_methods.php";

abstract class UserData {

    protected $user_login;
    protected $user_first_name;
    protected $user_last_name;
    protected $user_password;
    protected $user_password_hash;
    protected $error_log = array();

    //public methods

    public function get_error_log() {
        return $this->error_log;
    }

    //protected methods

    protected function error($err) {
        array_push($this->error_log, $err);
    }

    protected function is_data_empty() {
        if (array_key_exists("user_login", $_POST) ||
            array_key_exists("user_password", $_POST)
        ) {
            return false;
        }
        return true;
    }

    //debug methods

    public function get_user_login() {
        return $this->user_login;
    }

    public function get_user_password() {
        return $this->user_password;
    }

}



class RegisterUser extends UserData {

    //public methods

    public function validate_register_data() {
        if ($this->is_data_empty()) {
            $this->error("User data error: login or password is empty");
            return false;
        }
        $this->user_login = $_POST["user_login"];
        $this->user_password = $_POST["user_password"];
        $this->user_password_hash = "";
        $this->user_first_name = "";
        $this->user_last_name = "";
        //$this->user_first_name = $_POST["user_first_name"];
        //$this->user_last_name = $_POST["user_last_name"];

        $this->validate_user_login();
        $this->validate_user_password();
        if (empty($this->error_log)) {
            return true;
        }
        return false;
    }

    public function hash_password() {
        $this->user_password_hash = password_hash($this->user_password, PASSWORD_DEFAULT);
    }

    public function create_user() {
        if ($this->user_password_hash == "") {
            $this->error("Password error: password hash is empty");
            return false;
        }

        $data_for_db = array(
            "user_login" => $this->user_login,
            "user_password_hash" => $this->user_password_hash
        );

        return $this->post_to_db($data_for_db);
    }

    //private methods

    private function validate_user_login() {
        $user_login = $this->get_user_login();
        if (empty($user_login)) {
            $this->error("User login error: empty user name");
        }
        elseif (!filter_var($user_login, FILTER_VALIDATE_EMAIL)) {
            $this->error("User login error: invalid user name");
        }
    }

    private function validate_user_password() {
        $user_password = $this->get_user_password();
        if (strlen($user_password) < 8) {
            $this->error("User password error: password should be at least 8 digits long");
        }
        elseif (strlen($user_password) > 35) {
            $this->error("User password error: password should be no more than 35 digits long");
        }
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



class SignInUser extends UserData {

    private $user_login_from_db;
    private $user_password_hash_from_db;

    //public methods

    public function check_sign_in_data() {
        if ($this->is_data_empty()) {
            $this->error("User data error: login or password is empty");
            return false;
        }

        $this->user_login = $_POST["user_login"];
        $this->user_password = $_POST["user_password"];
        
        if(!$this->get_login_and_password_from_db()) {
            $this->error("Wrong login or database error");
            return false;
        }

        if( $this->check_password() && $this->check_login() ) {
            return true;
        }

        $this->error("User data error: login or password is incorrect");
        return false;
    }

    //private methods

    private function check_password() {
        return password_verify($this->user_password, $this->user_password_hash_from_db);
    }

    private function check_login() {
        return $this->user_login == $this->user_login_from_db;
    }

    private function get_login_and_password_from_db() {
        $user_data = $this->get_from_db();
        if (!empty($user_data[0])) {
            $this->user_login_from_db = $user_data[0]["user_login"];
            $this->user_password_hash_from_db = $user_data[0]["user_password_hash"];
            return true;
        }
        return false;
    }

    private function get_from_db() {
        $db = new Database();
        if ($db->connect_to_db()) {
            $user_data = $db->get($this->user_login);
            if (empty($user_data)) {
                $this->error($db->get_error_log());
            }
            return $user_data;
        }

        $this->error($db->get_error_log());
        return array();
    }

    //debug methods

    public function get_all_properties() {
        return array(
            "user_login" => $this->user_login,
            "user_password" => $this->user_password,
            "user_login_from_db" => $this->user_login_from_db,
            "user_password_hash_from_db" => $this->user_password_hash_from_db
        );
    }
}
?>