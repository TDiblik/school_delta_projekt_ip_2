<?php
    require_once __DIR__ . "./../models/User.class.php";

    session_start();
    if ($_SESSION['authenticated'] != true) {
        header('Location: login.php');
        die();
    }

    function get_user_details() {
        return new User($_SESSION["user_id"], $_SESSION["username"], $_SESSION["is_admin"]);
    }
?>