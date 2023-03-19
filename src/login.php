<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        require_once __DIR__ . "/utils/shared_headers.html";
        require_once __DIR__ . "/utils/password_utils.php";
        require_once __DIR__ . "/utils/db_helper.php";
        session_start();

        $error = null;
        $username = "";
        if (!empty($_POST)) {
            $username = $_POST['username'];
            $error = 'Username and pasword must be longer than 1 character.';
            if (!empty($_POST['username']) && !empty($_POST['password']) && strlen($_POST['username']) > 1 && strlen($_POST['password']) > 1) {
                $username = $_POST['username'];
                $password = $_POST['password'];

                $connection = get_db_connection();

                $user_query = $connection->prepare("select id, login, password, is_admin from employees where login = :login limit 1;");
                $user_query->execute([
                    ":login" => $username
                ]);

                $user = $user_query->fetch();
                if (check_pass($password, $user["password"])) {
                    $_SESSION['authenticated'] = true;
                    $_SESSION['is_admin'] = $user['is_admin'];
                    $_SESSION["user_id"] = $user['id'];
                    $_SESSION['username'] = $user['login'];

                    header('Location: ./index.php');
                    return;
                }

                $error = 'Username nebo heslo špatně.';
            }
        }
    ?>

    <title>Login</title>

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

        .error-label {
            color: red;
            text-align: center;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <form method="post">
            <div>
                <label for="username"> Username: </label>
                <input type="input" id="username" name="username"
                    value="<?php echo $username; ?>"
                />
            </div>
            <div>
                <label for="password"> Password: </label>
                <input type="password" id="password" name="password" />
            </div>
            <button type="submit"> Přihlásit se </button>
            <label class="error-label"><?php echo $error; ?></label>
        </form>
    </div>

</body>
</html>