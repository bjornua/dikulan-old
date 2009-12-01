<?php
    class Model_Seatbooking {
        public $config_directory;
        public $db;
        public $tablename;
        
        function __construct($config_directory, Zend_Db_Adapter_Abstract $db, $tablename){
            $this->config_directory = $config_directory;
            $this->db = $db;
            $this->tablename = $tablename;
        }

        function get_taken_seats_ids(){
            $sql = "select `id` from `$this->tablename`";
            $result = $this->db->fetchAll($sql);
            return $result;
        }

        function get_content_checksum(){
            $result = $this->db->fetchAll("checksum table `$this->tablename`");
            return $result[0]['Checksum'];
        }
    }