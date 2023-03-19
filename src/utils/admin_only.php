<?php
    if ($_SESSION['is_admin'] != true) {
        require_once __DIR__ . "/not_found.php";
    }
?>