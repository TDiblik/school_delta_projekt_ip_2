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

        require_once __DIR__ . "/models/Room.class.php";
        
        
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
        $room = new Room($room["id"], $room["name"], $room["number"], $room["telephone"]);

        $possible_errors = [];
        $was_edit_success = null;
        if (!empty($_POST)) {
            $room = new Room($_POST["id"], $_POST["name"], $_POST["number"], $_POST["telephone"]);
            
            $connection = get_db_connection();
            $possible_errors = $room->validate($connection);

            if (count($possible_errors) == 0) {
                $update = $connection->prepare("update rooms set name = :name, number = :number, telephone = :telephone where id = :id");
                $was_edit_success = $update->execute([
                    ":id" => $room->id,
                    ":name" => $room->name,
                    ":number" => $room->number,
                    ":telephone" => $room->telephone
                ]);
            }
        }
    ?>

    <title>Založení místnosti</title>

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
    <h1>Upravení místnosti</h1>
    <form method="post">

        <input type="hidden" name="id" value="<?php echo $room->id ?>" />
        <div>
            <label for="name">Název</label>
            <input type="text" name="name" maxlength="50" required value="<?php echo $room->name ?? ""; ?>"/>
        </div>
        <div>
            <label for="number">Číslo</label>
            <input type="number" name="number" maxlength="5" required value="<?php echo $room->number ?? ""; ?>"/>
        </div>
        <div>
            <label for="telephone">Telefon</label>
            <input type="number" name="telephone" maxlength="5" value="<?php echo $room->telephone ?? ""; ?>"/>
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
    <a href="./rooms.php" style="float: right;">Zpět na list</a>
</body>
</html>