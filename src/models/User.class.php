<?php
    class User {
        public $user_id = "";
        public $username = "";
        public $is_admin = false;
        
        function __construct($_user_id, $_username, $_is_admin) {
            $this->user_id = $_user_id;
            $this->username = $_username;
            $this->is_admin = $_is_admin;
        }
    }
?>