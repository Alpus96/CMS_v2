<?php
    require_once 'app/library/debug/logger.php';
    require_once 'app/library/socket/MySQL_socket.php';
    require_once 'app/objects/users/Tokens.php';

    class User extends MySQL_socket {

        static private $logger;
        static private $logName;

        static private $id;
        static private $username;
        static private $hash;
        static private $type;
        static private $locked;

        static private $getQuery;
        static private $token;
        static private $setPWQuery;

        function __construct ($identifier) {
            parent::__construct();

            self::$logger = new logger();
            self::$logName = '_userModel_errorLog';

            self::$getQuery = 'SELECT * FROM USERS WHERE USERNAME = ?';
            self::$setPWQuery = 'UPDATE USERS SET HASH = ? WHERE ID = ?';

            if (is_string($identifier)) { self::$token = new Token($identifier); }
            else if (is_object($identifier)) { self::getValidUser($identifier); }
        }

        function getToken() {
            return self::$token->toJSON();
        }

        function logout() {
            return self::$token->deleteToken();
        }

        function newPassword($password) {
            if (!is_string($password) || strlen($password) < 6) { return false; }
            if (self::$token->toJSON()) {
                $password = password_hash($password, PASSWORD_DEFAULT);
                self::$id = self::$token->decodedToken()->id;
                $connObj = parent::connect();
                if (!$connObj->error) {
                    $connection = $connObj->connection;
                    if ($query = $connection->prepare(self::$setPWQuery)) {
                        $query->bind_param('si', $password, self::$id);
                        $query->execute();
                        $success = $query->affected_rows > 0 ? true : false;
                        $query->close();
                        $connection->close();
                        return $success;
                    } else { $connection->close(); }
                } else {
                    $msg = 'Unable to connect to the database : '.$connObj->connection;
                    self::$logger->log(self::$logName, $msg);
                    throw new Exception($msg);
                }
            }
            return false;
        }

        private function getValidUser ($identifier) {
            $connObj = parent::connect();
            if (!$connObj->error) {
                $connection = $connObj->connection;
                if ($query = $connection->prepare(self::$getQuery)) {
                    $query->bind_param('s', $identifier->username);
                    $query->execute();
                    $query->bind_result(
                        self::$id,
                        self::$username,
                        self::$hash,
                        self::$type,
                        self::$locked
                    );
                    $query->fetch();
                    $query->close();
                    $connection->close();
                } else { $connection->close(); }
            } else {
                $msg = 'Unable to connect to the database : '.$connObj->connection;
                self::$logger->log(self::$logName, $msg);
                throw new Exception($msg);
            }

            if (isset(self::$id) && isset(self::$username) && isset(self::$hash) && isset(self::$type) && isset(self::$locked)) {
                if (password_verify($identifier->password, self::$hash)) {
                    $userObj = (object) [
                        'id' => self::$id,
                        'username' => self::$username,
                        'hash' => self::$hash,
                        'type' => self::$type,
                        'locked' => self::$locked
                    ];
                    self::$token = new Token($userObj);
                    return;
                }
            }
            self::$token = new Token(null);
        }

    }
?>