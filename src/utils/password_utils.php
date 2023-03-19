<?php
    function hash_pass($pass) {
        return password_hash($pass, PASSWORD_DEFAULT);
    }
    function check_pass($clear_text, $hash) {
        return password_verify($clear_text, $hash);
    }
?>