<?php
    class Room {
        public $id;
        public $name;
        public $number;
        public $telephone;

        function __construct($_id, $_name, $_number, $_telephone)
        {
            $this->id = $_id;
            $this->name = $_name;
            $this->number = $_number;
            $this->telephone = $_telephone == '' ? null : $_telephone;
        }

        function validate($sql_connection) {
            $errors_list = [];

            $room_name_query = $sql_connection->prepare("select true from rooms where name = :name and id != :id limit 1;");
            $room_name_query->execute([":name" => $this->name, ":id" => $this->id]);
            $room_name_valid = $room_name_query->fetch();
            if ($room_name_valid == true) {
                array_push($errors_list, "Název místnosti již existuje");
            }
            
            $room_number_query = $sql_connection->prepare("select true from rooms where number = :number and id != :id limit 1;");
            $room_number_query->execute([":number" => $this->number, ":id" => $this->id]);
            $room_number_valid = $room_number_query->fetch();
            if ($room_number_valid == true) {
                array_push($errors_list, "Číslo místnosti již existuje.");
            }

            $room_telephone_query = $sql_connection->prepare("select true from rooms where telephone = :telephone and id != :id limit 1;");
            $room_telephone_query->execute([":telephone" => $this->telephone, ":id" => $this->id]);
            $room_number_valid = $room_telephone_query->fetch();
            if ($room_number_valid == true) {
                array_push($errors_list, "Telefon místnosti již existuje.");
            }

            return $errors_list;
        }
    }
?>