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

        $room_id = $_GET["id"];
        $connection = get_db_connection();
        $room_query = $connection->prepare("select * from rooms where id = :id limit 1;");
        $room_query->execute([":id" => $room_id]);
        $room = $room_query->fetch();

        if ($room == false || $room == null) {
            require_once __DIR__ . "/utils/not_found.php";
        }

        $employees_in_room_query = $connection->prepare("select e.id, concat(e.first_name, ' ', left(e.last_name, 1), '.') as formatted_name from employees as e where room_id = :room_id;");
        $employees_in_room_query->execute([":room_id" => $room_id]);

        $avg_salary_query = $connection->prepare("select avg(salary) from employees where room_id = :room_id;");
        $avg_salary_query->execute([":room_id" => $room_id]);
        $avg_salary = $avg_salary_query->fetchColumn();

        $employees_with_keys_query = $connection->prepare("
            select e.id, concat(e.first_name, ' ', left(e.last_name, 1), '.') as formatted_name
                from active_keys as k
                left join employees e on k.employee_id = e.id
            where k.room_id = :room_id;
        ");
        $employees_with_keys_query->execute([":room_id" => $room_id]);
    ?>
    <title>Místnost č.: <?php echo $room["number"]; ?></title>
</head>
<body>
    <div class="container">
        <h1>Místnost č.: <?php echo $room["number"]; ?></h1>
        <dl>
            <dt>Číslo</dt>
            <dd> <?php echo $room["number"] ?> </dd>
            <dt>Název</dt>
            <dd> <?php echo $room["name"] ?> </dd>
            <dt>Telefon</dt>
            <dd> <?php echo $room["telephone"] ?? "-" ?> </dd>
            <dt>Lidé v místností</dt>
            <dd>
                <?php
                    if ($employees_in_room_query->rowCount() == 0) {
                        echo "<dd>-</dd>";
                    }
                    foreach ($employees_in_room_query as $employees_in_room) {
                        echo "<dd><a href=\"./employee.php?id=" . $employees_in_room["id"] . "\">" . $employees_in_room["formatted_name"] . "</a></dd>";
                    }
                ?>
            </dd>
            <dt>Průměrná mzda</dt>
            <dd> <?php echo $avg_salary ?? "-" ?> </dd>
            <dt>Lidé s klíči k místností</dt>
            <dd>
                <?php
                    if ($employees_with_keys_query->rowCount() == 0) {
                        echo "<dd>-</dd>";
                    }
                    foreach ($employees_with_keys_query as $employees_with_keys) {
                        echo "<dd><a href=\"./employee.php?id=" . $employees_with_keys["id"] . "\">" . $employees_with_keys["formatted_name"] . "</a></dd>";
                    }
                ?>
            </dd>
        </dl>
        <a href="./rooms.php" style="float: right;">Zpět na seznam místností</a>
    </div>
</body>
</html>