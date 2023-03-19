
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

        
        if (!isset($_GET["id"]) || $_GET["id"] === null || trim($_GET["id"]) === "") {
            require_once __DIR__ . "/utils/bad_request.php";
        }

        $employee_id = $_GET["id"];
        $employee_query = $connection->prepare("select * from employees where id = :id limit 1;");
        $employee_query->execute([":id" => $employee_id]);
        $employee = $employee_query->fetch();

        if ($employee == false || $employee == null) {
            require_once __DIR__ . "/utils/not_found.php";
        }
        $employee = new Employee($employee["id"], $employee["first_name"], $employee["last_name"], $employee["salary"], $employee["job_title_id"], $employee["room_id"], $employee["login"], $employee["is_admin"]);

        $job_titles_query = $connection->prepare("select id, name from job_titles;");
        $job_titles_query->execute();
        $job_titles = $job_titles_query->fetchAll();

        $rooms_query = $connection->prepare("select id, name from rooms;");
        $rooms_query->execute();
        $rooms = $rooms_query->fetchAll();

        $possible_errors = [];
        $was_edit_success = null;
        if (!empty($_POST)) {
            $employee = new Employee($_POST["id"], $_POST["first_name"], $_POST["last_name"], $_POST["salary"], $_POST["job_title"], $_POST["room_id"], $_POST["login"], $_POST["is_admin"] ?? false);
            var_dump($employee);
            
            $connection = get_db_connection();
            $possible_errors = $employee->validate($connection);

            if (count($possible_errors) == 0) {
                $update = $connection->prepare("
                    update employees set first_name = :first_name, last_name = :last_name, salary = :salary, job_title_id = :job_title_id, room_id = :room_id, login = :login, is_admin = :is_admin where id = :id
                ");

                $was_edit_success = $update->execute([
                    ":first_name" => $employee->first_name,
                    ":last_name" => $employee->last_name,
                    ":salary" => $employee->salary,
                    ":job_title_id" => $employee->job_title_id,
                    ":room_id" => $employee->room_id,
                    ":login" => $employee->login,
                    ":is_admin" => $employee->is_admin,
                    ":id" => $employee->id
                ]);
            }
        }
    ?>

    <title>Úprava uživatele</title>

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
    <h1>Úprava uživatele</h1>
    <button style="float: right"><a href="./edit_keys.php?user_id=<?php echo $employee->id ?>"> Upravit klíče </a></button>
    <form method="post">
        <input type="hidden" name="id" value="<?php echo $employee->id ?>" />
        <div>
            <label for="first_name">Jméno</label>
            <input type="text" name="first_name" maxlength="30" required  value="<?php echo $employee->first_name; ?>"/>
        </div>
        <div>
            <label for="last_name">Příjmení</label>
            <input type="text" name="last_name" maxlength="30" required value="<?php echo $employee->last_name; ?>"/>
        </div>
        <div>
            <label for="salary">Výplata</label>
            <input type="number" name="salary" maxlength="12" required value="<?php echo $employee->salary; ?>"/>
        </div>
        <div>
            <label for="job_title"> Job title </label>
            <select name="job_title"> 
                <?php
                    foreach ($job_titles as &$job_title) {
                        echo '<option value=' . $job_title["id"] . ' ' . ($job_title["id"] == $employee->job_title_id  ? "selected" : "") . '>'.$job_title["name"].'</option>';
                    }
                ?>
            </select>
        </div>
        <div>
            <label for="room_id"> Místnost </label>
            <select name="room_id"> 
                <?php
                    foreach ($rooms as &$room) {
                        echo '<option value=' . $room["id"] . ' ' . ($room["id"] == $employee->room_id  ? "selected" : "") . '>'.$room["name"].'</option>';
                    }
                ?>
            </select>
        </div>
        <div>
            <label for="login">login</label>
            <input type="text" name="login" maxlength="30" required value="<?php echo $employee->login; ?>"/>
        </div>
        <div>
            <label for="is_admin">Je admin?</label>
            <input type="checkbox" name="is_admin" <?php echo $employee->is_admin ? "checked" : "" ?> />
        </div>

        <button type="submit">Upravit</button>
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
    <?php 
        if ($was_edit_success == true) {
            echo "<label>Úspěšně uloženo.</label>";
        } else if ($was_edit_success == false && $was_edit_success != null) {
            echo "<label>Oops něco se nepovedlo</label>";
        }
    ?>
    <a href="./employees.php" style="float: right;">Zpět na list</a>
</body>
</html>