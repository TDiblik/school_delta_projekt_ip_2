<?php
    require_once __DIR__ . "/utils/check_auth.php";
    require_once __DIR__ . "/utils/db_helper.php";

    $title = "Seznam zaměstnanců";
    $default_sort_column = "employee_name";
    $default_sort_direction = "asc";
    $column_names = [
        ["db_name" => "employee_name", "visible_name" => "Jméno", "link" => "./employee.php?id="],
        ["db_name" => "room_name", "visible_name" => "Místnost"],
        ["db_name" => "room_telephone", "visible_name" => "Telefon"],
        ["db_name" => "job_title", "visible_name" => "Pozice"],
    ];
    $new_link = "./employee_new.php";
    $edit_link = "./employee_edit.php?id=";
    $base_query = 
        "select e.id as id, concat(e.first_name, ' ', e.last_name) as employee_name, r.name as room_name, r.telephone as room_telephone, j.name as job_title from employees as e
            left join rooms r on e.room_id = r.id
            left join job_titles j on e.job_title_id = j.id
        order by ";
    
    $info_msg = "";
    if (!empty($_POST) && !empty($_POST["id_to_delete"])) {
        require_once __DIR__ . "/utils/admin_only.php";
        $employee_id_to_delete = $_POST["id_to_delete"];

        $connection = get_db_connection();

        $delete_keys = $connection->prepare("delete from active_keys where employee_id = :employee_id");
        $delete_keys->execute([":employee_id" => $employee_id_to_delete]);
        $delete_employee = $connection->prepare("delete from employees where id = :employee_id");
        $delete_employee->execute([":employee_id" => $employee_id_to_delete]);

        $info_msg = "Člověk úspěšně smazán. Smazány i klíče k místnostem.";
    }

    require_once __DIR__ . '/utils/shared_list_component.php';
?>