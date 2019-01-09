<?php
    class Session {
        private $cookiesExpirationTime;

        function __construct() {
            $this->cookiesExpirationTime = 24 * 3600; //one day
        }

        public function start_session() {
            session_start([
                'cookie_lifetime' => $this->cookiesExpirationTime,
            ]);
        }

        public function delete_cookies() {
            
        }

        public function check_cookies() {

        }
    }

?>