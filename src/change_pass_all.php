<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        require_once __DIR__ . "/utils/check_auth.php";
        require_once __DIR__ . "/utils/admin_only.php";

        require_once __DIR__ . "/utils/shared_headers.html";
        require_once __DIR__ . "/utils/db_helper.php";
        require_once __DIR__ . "/utils/password_utils.php";

        $user_details = get_user_details();

        $connection = get_db_connection();

        $was_save_success = null;
        $user_id = "";
        if (!empty($_POST) && !empty($_POST['user_id']) && strlen($_POST['user_id']) > 1 && !empty($_POST['password']) && strlen($_POST['password']) > 1) {
            $user_id = $_POST['user_id'];
            $room_query = $connection->prepare("update employees set password = :password where id = :id;");
            $was_save_success = $room_query->execute([
                ":password" => hash_pass($_POST['password']), 
                ":id" => $user_id
            ]);
        }

        $users_query = $connection->prepare("select id, login from employees;");
        $users_query->execute();
        $users = $users_query->fetchAll();
    ?>
    <title>Změna hesla</title>

    <style>
    form {
        display: flex;
        flex-direction: column;
    }

    form > div {
        display: flex;
        flex-direction: row;
        margin-bottom: 10px;
        justify-content: center;
    }

    form > div > label {
        margin: 0 5px 0 5px;
    }

    form > button {
        margin: auto;
    }
    </style>
</head>
<body>
    <div class="container">
        <h1>Změna hesla uživatelů</h1>
        <form method="post">
            <div>
                <label for="user_id"> Uživatel na změnění: </label>
                <select name="user_id"> 
                    <?php
                        foreach ($users as &$user) {
                            echo '<option value=' . $user["id"] . ' ' . ($user["id"] == $user_id  ? "selected" : "") . '>'.$user["login"].'</option>';
                        }
                    ?>
                </select>
            </div>
            <div>
                <label for="password"> Nové heslo: </label>
                <input type="password" id="password" name="password" />
            </div>
            <button type="submit"> Změnit </button>
        </form>
        <?php 
            if ($was_save_success == true) {
                echo "<label>Úspěšně uloženo.</label>";
            } else if ($was_save_success == false && $was_save_success != null) {
                echo "<label>Oops něco se nepovedlo</label>";
            }
        ?>
        <a href="./index.php" style="float: right;">Zpět na index</a>
    </div>
</body>
</html>