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
        require_once __DIR__ . "/utils/password_utils.php";

        $user_details = get_user_details();

        if (!empty($_POST) && !empty($_POST['password']) && strlen($_POST['password']) > 1) {
            $connection = get_db_connection();
            $update_query = $connection->prepare("update employees set password = :password where id = :id;");
            $update_query->execute([
                ":password" => hash_pass($_POST['password']), 
                ":id" => $user_details->user_id
            ]);
            header('Location: logout.php');
            session_destroy();
            die();
        }
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
        <h1>Změna hesla</h1>
        <form method="post">
            <div>
                <label for="password"> Nové heslo: </label>
                <input type="password" id="password" name="password" />
            </div>
            <button type="submit"> Změnit </button>
        </form>
        <a href="./index.php" style="float: right;">Zpět na index</a>
    </div>
</body>
</html>