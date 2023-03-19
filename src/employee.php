<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        require_once __DIR__ . "/utils/check_auth.php";
        require_once __DIR__ . "/utils/shared_headers.html";
        require_once __DIR__ . "/utils/db_helper.php";

        if (!isset($_GET["id"]) || $_GET["id"] === null || trim($_GET["id"]) === "") {
            require_once __DIR__ . "/utils/bad_request.php";
        }

        $user_id = $_GET["id"];
        $connection = get_db_connection();
        $user_query = $connection->prepare("
            select  e.first_name, e.last_name, e.salary, concat(e.first_name, ' ', e.last_name) as full_name, concat(left(e.last_name, 1), '.') as last_name_short,
                    j.name as job_title_name,
                    e.room_id, r.name as room_name
                from employees as e
                left join job_titles j on e.job_title_id = j.id
                left join rooms r on e.room_id = r.id
                where e.id = :id
            limit 1;
        ");
        $user_query->execute([":id" => $user_id]);
        $user = $user_query->fetch();

        if ($user == false || $user == null) {
            require_once __DIR__ . "/utils/not_found.php";
        }

        $room_keys_query = $connection->prepare("
            select r.name as room_name, r.id as room_id
                from active_keys as k
                left join rooms r on k.room_id = r.id
            where k.employee_id = :id;
        ");
        $room_keys_query->execute([":id" => $user_id]);
    ?>
    <title>Karta osoby: <?php echo $user["full_name"]; ?></title>
</head>
<body>
    <div class="container">
        <h1>Karta osoby: <?php echo $user["first_name"] . " " . $user["last_name_short"]; ?> </h1>
        <dl>
            <dt>Jméno</dt>
            <dd> <?php echo $user["first_name"] ?> </dd>
            <dt>Příjmení</dt>
            <dd> <?php echo $user["last_name"] ?> </dd>
            <dt>Pozice</dt>
            <dd> <?php echo $user["job_title_name"] ?> </dd>
            <dt>Mzda</dt>
            <dd> <?php echo $user["salary"] ?> </dd>
            <dt>Místnost</dt>
            <dd> <?php echo "<a href=\"./room.php?id=" . $user["room_id"] . "\">" . $user["room_name"] . "</a>" ?> </dd>
            <dt>Klíče</dt>
            <dd>
                <?php
                    if ($room_keys_query->rowCount() == 0) {
                        echo "<dd>-</dd>";
                    }
                    foreach ($room_keys_query as $room_key) {
                        echo "<dd><a href=\"./room.php?id=" . $room_key["room_id"] . "\">" . $room_key["room_name"] . "</a></dd>";
                    }
                ?>
            </dd>
        </dl>
        <a href="./employees.php" style="float: right;">Zpět na seznam zaměstnanců</a>
    </div>
</body>
</html>