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

        $users_query = $connection->prepare("select id, login from employees;");
        $users_query->execute();
        $users = $users_query->fetchAll();

        $selected_user = null;
        $user_id = "";
        if (!empty($_GET["user_id"])) {
            $user_id = $_GET["user_id"];
            foreach ($users as &$user_loop) {
                if ($user_loop["id"] == $user_id) {
                    $selected_user = $user_loop;
                }
            }
            unset($user_loop);
        }

        $rooms_query = $connection->prepare("select id, name from rooms order by name;");
        $rooms_query->execute();
        $rooms = $rooms_query->fetchAll();

        $was_save_success = null;
        if (!empty($_POST) && !empty($user_id)) {
            $rooms_in_question = [];
            foreach ($rooms as &$room) {
                if (!empty($_POST[$room["id"]])) {
                    array_push($rooms_in_question, $room["id"]);
                }
            }
            unset($room);

            $del_keys = $connection->prepare("delete from active_keys where employee_id = :employee_id;");
            $del_keys->execute([":employee_id" => $user_id]);

            $sql_query = "insert into active_keys (employee_id, room_id) values ";
            $arguments = [];
            for ($i = 0; $i < count($rooms_in_question); $i++) {
                $room = $rooms_in_question[$i];
                $named_argument = ":employee_id" . $i;
                $arguments[$named_argument] = $user_id;
                $sql_query .= "($named_argument, '" . $room . "'),";
            }
            $sql_query = trim($sql_query, ',');
            $insert_keys = $connection->prepare($sql_query);
            $was_save_success = $insert_keys->execute($arguments);
        }

        $active_keys_query = $connection->prepare("select room_id from active_keys where employee_id = :employee_id;");
        $active_keys_query->execute([":employee_id" => $user_id]);
        $active_keys = $active_keys_query->fetchAll();
    ?>
    <title>Změna klíčů</title>

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

    <script>
        function redirect() {
            window.location.href = "./edit_keys.php?user_id=" + document.getElementById("user_id").value;
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Změna klíčů uživatelů</h1>
        <form method="post">
            <div>
                <label for="user_id"> Uživatel na změnění: </label>
                <select id="user_id" name="user_id" onchange="redirect()"> 
                    <option value=""></option>
                    <?php
                        foreach ($users as &$user) {
                            echo '<option value="' . $user["id"] . '" ' . ($user["id"] == $user_id ? "selected" : "") . '>'.$user["login"].'</option>';
                        }
                    ?>
                </select>
            </div>
            <?php
                if ($selected_user != null) {
                    foreach ($rooms as &$room) {
                        $should_be_check = false;
                        foreach ($active_keys as &$active_key) {
                            if ($active_key["room_id"] == $room["id"]) {
                                $should_be_check = true;
                                break;
                            }
                        }
                        echo '
                            <div>
                                <label>' . $room["name"] . ': </label>
                                <input name="'.$room["id"].'" value="'.$room["id"].'" type="checkbox" ' . ($should_be_check ? "checked" : "") . ' />
                            </div>
                        ';
                    }

                    echo '
                        <button type="submit"> Nastavit </button>
                    ';
                }
            ?>
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