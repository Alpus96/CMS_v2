<?php
    class adminModel extends mysqlSocket {

        static private $query;

        protected function __construct () {
            parent::__construct();

            self::$query->get_users = 'SELECT USERNAME, TYPE, LOCKED FROM USERS';
            self::$query->is_user = 'SELECT ID FROM USERS WHERE USERNAME = ?';
            self::$query->create = 'INSERT INTO USERS SET USERNAME = ?, HASH = ?, TYPE = ?';
            self::$query->ud_type = 'UPDATE USERS SET TYPE = ? WHERE USERNAME = ?';
            self::$query->toggle_lock = 'UPDATE USERS SET LOCKED = NOT LOCKED WHERE USERNAME = ?';
            self::$query->delete = 'DELETE FROM USERS WHERE USERNAME = ?';
        }

        protected function createUser () {

        }

        protected function getUser () {

        }

        protected function getAll () {

        }

        protected function updateType () {

        }

        protected function toggleLock () {

        }

        protected function delete () {

        }

        private function handleError () {

        }

    }
?>