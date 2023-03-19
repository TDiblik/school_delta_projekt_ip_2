<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        require_once __DIR__ . "/utils/check_auth.php";
        require_once __DIR__ . "/utils/shared_headers.html";

        $user_details = get_user_details();
    ?>
    <title>Prohlížeč databáze</title>
</head>
<body>
    <div class="container">
        <h1>Prohlížeč databáze</h1>
        <ul class="list-group">
            <li class="list-group-item"><a href="./employees.php"> Seznam zaměstnanců </a></li>
            <li class="list-group-item"><a href="./rooms.php"> Seznam místností </a></li>
            <li class="list-group-item"><a href="./change_pass.php"> Změna hesla </a></li>
            <?php
                if ($user_details->is_admin) {
                    echo "<li class=\"list-group-item\"><a href=\"./change_pass_all.php\"> Změna hesla uživatelů </a></li>";
                    echo "<li class=\"list-group-item\"><a href=\"./edit_keys.php\"> Upravit klíče </a></li>";
                }
            ?>
            <li class="list-group-item"><a href="./logout.php"> Odhlásit </a></li>
        </ul>
    </div>
</body>
</html>