<?php
    require_once __DIR__ . "./../utils/db_helper.php";
    require_once __DIR__ . "./../utils/password_utils.php";

    $connection = get_db_connection();

    $delete_query = $connection->prepare("
        delete from employees where login = :admin_login or login = :user_login;
    ");
    $delete_query->execute([
        ":admin_login" => "admin",
        ":user_login" => "user"
    ]);

    $insert_query = $connection->prepare("
        insert into employees (first_name, last_name, salary, job_title_id, room_id, login, password, is_admin)
        values ('Admin', 'Hamáčková', 32000.00, (select id from job_titles where name like 'ekonom(ka)'), (select id from rooms where name like 'Ekonomické'), :admin_login, :admin_pass, true),
               ('User', 'Holzer', 22000.00, (select id from job_titles where name like 'techni(k/čka)'), (select id from rooms where name like 'Dílna'), :user_login, :user_pass, false);
    ");
    $insert_query->execute([
        ":admin_login" => "admin",
        ":user_login" => "user",
        ":admin_pass" => hash_pass("admin"),
        ":user_pass" => hash_pass("user")
    ]);
?>