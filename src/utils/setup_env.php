<?php
    $env_file = fopen(realpath(__DIR__ . "/../.env"), "r") or die("Unable to read env file");
    while (!feof($env_file)) {
        list($name, $value) = explode("=", fgets($env_file), 2);

        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
        }
    }
    fclose($env_file);