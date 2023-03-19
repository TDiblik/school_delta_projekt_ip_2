<?php
    class Employee {
        public $id;
        public $first_name;
        public $last_name;
        public $salary;
        public $job_title_id;
        public $room_id;
        public $login;
        public $is_admin;

        function __construct($_id, $_first_name, $_last_name, $_salary, $_job_title_id, $_room_id, $_login, $_is_admin)
        {
            $this->id = $_id;
            $this->first_name = $_first_name;
            $this->last_name = $_last_name;
            $this->salary = $_salary;
            $this->job_title_id = $_job_title_id;
            $this->room_id = $_room_id;
            $this->login = $_login;
            $this->is_admin = ($_is_admin != false) ? 1 : 0;
        }

        function validate($sql_connection) {
            $errors_list = [];

            $titles_id_query = $sql_connection->prepare("select true from job_titles where id = :job_title_id limit 1;");
            $titles_id_query->execute([":job_title_id" => $this->job_title_id]);
            if ($titles_id_query->fetch() != true) {
                array_push($errors_list, "Id vybraného job titlu neexistuje.");
            }

            $rooms_query = $sql_connection->prepare("select true from rooms where id = :room_id limit 1;");
            $rooms_query->execute([":room_id" => $this->room_id]);
            if ($rooms_query->fetch() != true) {
                array_push($errors_list, "Id vybrané místnosti neexistuje.");
            }

            $login_query = $sql_connection->prepare("select true from employees where login = :login and id != :id limit 1;");
            $login_query->execute([":login" => $this->login, ":id" => $this->id]);
            if ($login_query->fetch() == true) {
                array_push($errors_list, "Login musí být unikátní.");
            }

            return $errors_list;
        }
    }
?>