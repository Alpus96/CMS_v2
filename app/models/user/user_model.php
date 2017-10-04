<?php
    class UserModel extends MySQL_socket
    {
        static private $logger;
        static private $logName;

        private static $getByUsernameStmt;
        private static $getByIdStmt;

        private static $id;
        private static $username;
        private static $hash;
        private static $type;

        protected function __construct ($identifier = null) {
            parent::__construct();

            self::$logger = new logger();
            self::$logName = '_userModel_log';

            self::$getByUsernameStmt = 'SELECT * FROM USERS WHERE USERNAME = ? LIMIT 1';
            self::$getByIdStmt = 'SELECT * FROM USERS WHERE ID = ? LIMIT 1';

            $this->identify($identifier);
        }

        protected static function toObject() {
            if (self::$id && self::$username && self::$hash && self::$type) {
                return (object) [
                    'id' => self::$id,
                    'username' => self::$username,
                    'hash' => self::$hash,
                    'type' => self::$type
                ];
            } else { return false; }
        }

        protected function identify ($identifier) {
            if (is_int($identifier)) { $this->getById($identifier); }
            else if (is_string($identifier)) { $this->getByUsername($identifier); }
        }

        private function getById ($id) {
            $mysql = parent::connect();
            if (!$mysql->error) {
                if ($query = $mysql->connection->prepare(self::$getByIdStmt)) {
                    $query->bind_param('i', $id);
                    $query->execute();

                    $query->bind_result($id, $username, $hash, $type);
                    while ($query->fetch()) {
                        self::$id = (integer)$id;
                        self::$username = $username;
                        self::$hash = $hash;
                        self::$type = $type;
                    }

                    $query->close();
                    $mysql->connection->close();
                } else {
                    $this->error = 'Unable to prepare get by id query.';
                    self::$logger->log(self::$logName, $this->error);
                }
            } else {
                $this->error = 'Unable to connect to database, see MySQL_socket log for details.';
                self::$logger->log(self::$logName, $this->error);
            }
        }

        private function getByUsername ($username) {
            $mysql = parent::connect();
            if (!$mysql->error) {
                if ($query = $mysql->connection->prepare(self::$getByUsernameStmt)) {
                    $query->bind_param('s', $username);
                    $query->execute();

                    $query->bind_result($id, $username, $hash, $type);
                    while ($query->fetch()) {
                        self::$id = $id;
                        self::$username = $username;
                        self::$hash = $hash;
                        self::$type = $type;
                    }

                    $query->close();
                    $mysql->connection->close();
                } else {
                    $this->error = 'Unable to prepare get by username query.';
                    self::$logger->log(self::$logName, $this->error);
                }
            } else {
                $this->error = 'Unable to connect to database, see MySQL_socket log for details.';
                self::$logger->log(self::$logName, $this->error);
            }
        }

    }
?>