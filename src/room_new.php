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

        $possible_errors = [];
        if (!empty($_POST)) {
            $room = new Room("", $_POST["name"], $_POST["number"], $_POST["telephone"]);
            
            $connection = get_db_connection();
            $possible_errors = $room->validate($connection);

            if (count($possible_errors) == 0) {
                $insertion = $connection->prepare("insert into rooms (name, number, telephone) values (:name, :number, :telephone)");
                $was_success = $insertion->execute([
                    ":name" => $room->name,
                    ":number" => $room->number,
                    ":telephone" => $room->telephone
                ]);

                header('Location: ./rooms.php');
                return;
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
    <h1>Založení místnosti</h1>
    <form method="post">
        <div>
            <label for="name">Název</label>
            <input type="text" name="name" maxlength="50" required  value="<?php echo $_POST["name"] ?? ""; ?>"/>
        </div>
        <div>
            <label for="number">Číslo</label>
            <input type="number" name="number" maxlength="5" required value="<?php echo $_POST["number"] ?? ""; ?>"/>
        </div>
        <div>
            <label for="telephone">Telefon</label>
            <input type="number" name="telephone" maxlength="5" value="<?php echo $_POST["telephone"] ?? ""; ?>"/>
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
    <a href="./rooms.php" style="float: right;">Zpět na list</a>
</body>
</html>