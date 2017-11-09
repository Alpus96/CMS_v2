<?php
    require_once 'app/library/debug/logger.php';
    require_once 'app/library/socket/MySQL_socket.php';
    require_once 'app/objects/users/Tokens.php';

    class User extends MySQL_socket {

        static private $logger;
        static private $logName;

        static private $getQuery;
        static private $token;
        static private $user;
        static private $setPWQuery;
        static private $setAuthorNameQuery;

        function __construct ($identifier) {
            parent::__construct();

            self::$logger = new logger();
            self::$logName = '_userModel_errorLog';

            self::$getQuery = 'SELECT * FROM USERS WHERE USERNAME = ?';
            self::$setPWQuery = 'UPDATE USERS SET HASH = ? WHERE ID = ?';
            self::$setAuthorNameQuery = 'UPDATE USERS SET AUTHOR_NAME = ? WHERE ID = ?';

            if (is_string($identifier)) {
                self::$token = new Token($identifier);
                if (self::$token->toJSON()) { self::$user = self::$token->decodedToken(); }
            } else if (is_object($identifier)) {
                self::getValidUser($identifier);
            }
        }

        public function getToken() {
            return self::$token->toJSON();
        }

        public function getAuthorName () {
            return self::$token->decodedToken()->authName;
        }

        public function logout() {
            return self::$token->deleteToken();
        }

        public function setAuthorName ($password, $authorName) {
            if (!is_string($authorName)) { return false; }
            if (self::$token->toJSON() && password_verify($password, self::$user->hash)) {
                $connObj = parent::connect();
                if (!$connObj->error) {
                    $connection = $connObj->connection;
                    if ($query = $connection->prepare(self::$setAuthorNameQuery)) {
                        $query->bind_param('si', $authorName, self::$user->id);
                        $query->execute();
                        $success = $query->affected_rows > 0 ? true : false;
                        $query->close();
                        $connection->close();
                        if ($success) {
                            self::$user->authName = $authorName;
                            self::$token = new Token(self::$user);
                        }
                        return $success ? self::$token->toJSON() : false;
                    } else { $connection->close(); }
                } else { self::databaseError($connObj->connection); }
            }
            return false;
        }

        public function newPassword($password, $newPass) {
            if (!is_string($newPass) || strlen($newPass) < 6) { return false; }
            if (self::$token->toJSON() && password_verify($password, self::$user->hash)) {
                $password = password_hash($newPass, PASSWORD_DEFAULT);
                $connObj = parent::connect();
                if (!$connObj->error) {
                    $connection = $connObj->connection;
                    if ($query = $connection->prepare(self::$setPWQuery)) {
                        $query->bind_param('si', $password, self::$user->id);
                        $query->execute();
                        $success = $query->affected_rows > 0 ? true : false;
                        $query->close();
                        $connection->close();
                        if ($success) {
                            self::$user->hash = $password;
                            self::$token = new Token(self::$user);
                        }
                        return $success ? self::$token->toJSON() : false;
                    } else { $connection->close(); }
                } else { self::databaseError($connObj->connection); }
            }
            return false;
        }

        private function getValidUser ($identifier) {
            if (!property_exists($identifier, 'username') || !property_exists($identifier, 'password')) { return false; }
            $connObj = parent::connect();
            if (!$connObj->error) {
                $connection = $connObj->connection;
                if ($query = $connection->prepare(self::$getQuery)) {
                    $query->bind_param('s', $identifier->username);
                    $query->execute();
                    $query->bind_result(
                        $id,
                        $username,
                        $authName,
                        $hash,
                        $type,
                        $locked
                    );
                    $query->fetch();
                    if (!$locked) {
                        self::$user = (object)[
                            'id' => $id,
                            'username' => $username,
                            'authName' => $authName,
                            'hash' => $hash,
                            'type' => $type,
                            'locked' => $locked
                        ];
                    }
                    $query->close();
                    $connection->close();
                } else { $connection->close(); }
            } else { self::databaseError($connObj->connection); }

            if (isset(self::$user->id) && isset(self::$user->username) && isset(self::$user->hash) && isset(self::$user->type) && isset(self::$user->locked)) {
                if (password_verify($identifier->password, self::$user->hash)) {
                    self::$token = new Token(self::$user);
                    return;
                }
            }
            self::$token = new Token(null);
        }

        private static function databaseError($error) {
            $msg = 'Unable to connect to the database : '.$error;
            self::$logger->log(self::$logName, $msg);
            throw new Exception($msg);
        }

    }
?>