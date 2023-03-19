<?php
    require_once __DIR__ . "/utils/check_auth.php";
    require_once __DIR__ . "/utils/db_helper.php";

    $title = "Seznam místností";
    $default_sort_column = "room_name";
    $default_sort_direction = "asc";
    $column_names = [
        ["db_name" => "room_name", "visible_name" => "Název", "link" => "./room.php?id="],
        ["db_name" => "room_number", "visible_name" => "Číslo"],
        ["db_name" => "room_telephone", "visible_name" => "Telefon"],
    ];
    $new_link = "./room_new.php";
    $edit_link = "./room_edit.php?id=";
    $base_query = "select id as id, name as room_name, number as room_number, telephone as room_telephone from rooms order by ";
    
    $info_msg = "";
    if (!empty($_POST) && !empty($_POST["id_to_delete"])) {
        require_once __DIR__ . "/utils/admin_only.php";
        $room_id_to_delete = $_POST["id_to_delete"];

        $connection = get_db_connection();
        $room_cannot_be_deleted_query = $connection->prepare("select 1 from rooms as r right join employees e on r.id = e.room_id where r.id = :room_id limit 1;");
        $room_cannot_be_deleted_query->execute([":room_id" => $room_id_to_delete]);
        $room_cannot_be_deleted_valid = $room_cannot_be_deleted_query->fetch();
        if ($room_cannot_be_deleted_valid != true) {

            $delete_keys = $connection->prepare("delete from active_keys where room_id = :room_id");
            $delete_keys->execute([":room_id" => $room_id_to_delete]);

            $delete_room = $connection->prepare("delete from rooms where id = :room_id");
            $delete_room->execute([":room_id" => $room_id_to_delete]);

            $info_msg = "Místnost úspěšně smazána. Smazány i klíče.";
        } else {
            $info_msg = "Někdo má tuto místnost jako místnost v které bydlí. Nemůžu smazat.";
        }
    }

    require_once __DIR__ . '/utils/shared_list_component.php';
?>