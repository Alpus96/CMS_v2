<?php

    require_once 'library/socket/mysqlSocket.php';
    require_once 'library/debug/logger.php';

    class adminModel extends mysqlSocket {

        static private $query;

        protected function __construct () {
            parent::__construct();

            self::$query->get_all = 'SELECT USERNAME, TYPE, LOCKED FROM USERS';
            self::$query->get_user = 'SELECT ID FROM USERS WHERE USERNAME = ?';
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

        /**
        *   @method     Enters a exception entry to the error log file.
        *
        *   @param      object|string : The faulty object or message to log as an exception.
        * */
        private function logError ($msg) {
            //  Open the log instance.
            $logger = new logger('tokenModel_errorsLog');
            //  Create an exception with the given prameter.
            $e = new Exception($msg);
            //  Log the exception.
            $logger->log('Unable to connect to database: '.$e);
        }

    }
?>