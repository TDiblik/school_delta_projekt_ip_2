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

        require_once __DIR__ . "/models/Employee.class.php";

        $connection = get_db_connection();

        $job_titles_query = $connection->prepare("select id, name from job_titles;");
        $job_titles_query->execute();
        $job_titles = $job_titles_query->fetchAll();

        $rooms_query = $connection->prepare("select id, name from rooms;");
        $rooms_query->execute();
        $rooms = $rooms_query->fetchAll();

        $possible_errors = [];

        if (!empty($_POST)) {
            $employee = new Employee("", $_POST["first_name"], $_POST["last_name"], $_POST["salary"], $_POST["job_title"], $_POST["room_id"], $_POST["login"], $_POST["is_admin"] ?? false);
            
            $connection = get_db_connection();
            $possible_errors = $employee->validate($connection);

            if (count($possible_errors) == 0) {
                $insertion = $connection->prepare("insert into employees (first_name, last_name, salary, job_title_id, room_id, login, password, is_admin) values (:first_name, :last_name, :salary, :job_title_id, :room_id, :login, :password, :is_admin)");
                $insertion->execute([
                    ":first_name" => $employee->first_name,
                    ":last_name" => $employee->last_name,
                    ":salary" => $employee->salary,
                    ":job_title_id" => $employee->job_title_id,
                    ":room_id" => $employee->room_id,
                    ":login" => $employee->login,
                    ":password" => hash_pass($_POST["new_password"]),
                    ":is_admin" => $employee->is_admin
                ]);

                header('Location: ./employees.php');
                return;
            }
        }
    ?>

    <title>Založení uživatele</title>

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
    <h1>Založení uživatele</h1>
    <form method="post">
        <div>
            <label for="first_name">Jméno</label>
            <input type="text" name="first_name" maxlength="30" required  value="<?php echo $_POST["first_name"] ?? ""; ?>"/>
        </div>
        <div>
            <label for="last_name">Příjmení</label>
            <input type="text" name="last_name" maxlength="30" required value="<?php echo $_POST["last_name"] ?? ""; ?>"/>
        </div>
        <div>
            <label for="salary">Výplata</label>
            <input type="number" name="salary" maxlength="12" required value="<?php echo $_POST["salary"] ?? ""; ?>"/>
        </div>
        <div>
            <label for="job_title"> Job title </label>
            <select name="job_title"> 
                <?php
                    foreach ($job_titles as &$job_title) {
                        echo '<option value=' . $job_title["id"] . ' ' . ($job_title["id"] == ($_POST["job_title"] ?? "")  ? "selected" : "") . '>'.$job_title["name"].'</option>';
                    }
                ?>
            </select>
        </div>
        <div>
            <label for="room_id"> Místnost </label>
            <select name="room_id"> 
                <?php
                    foreach ($rooms as &$room) {
                        echo '<option value=' . $room["id"] . ' ' . ($room["id"] == ($_POST["room_id"] ?? "")  ? "selected" : "") . '>'.$room["name"].'</option>';
                    }
                ?>
            </select>
        </div>
        <div>
            <label for="login">login</label>
            <input type="text" name="login" maxlength="30" required value="<?php echo $_POST["login"] ?? ""; ?>"/>
        </div>
        <div>
            <label for="new_password">nové heslo</label>
            <input type="text" name="new_password" maxlength="30" required value="<?php echo $_POST["new_password"] ?? ""; ?>"/>
        </div>
        <div>
            <label for="is_admin">Je admin?</label>
            <input type="checkbox" name="is_admin" <?php echo ($_POST["is_admin"] ?? false) ? "checked" : "" ?> />
        </div>

        <button type="submit">Založit</button>
    </form>
    <div style="color: red">
        <?php
            if (count($possible_errors) != 0) {
                echo "<label>Errory při zakládání:</label><br/>";
                foreach ($possible_errors as $error) {
                    echo "<label>" . $error . "</label><br/>";
                }
            }
        ?>
    </div>
    <a href="./employees.php" style="float: right;">Zpět na list</a>
</body>
</html>