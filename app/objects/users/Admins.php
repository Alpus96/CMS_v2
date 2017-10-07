<?php
    require_once 'app/library/debug/logger.php';
    require_once 'app/library/socket/MySQL_socket.php';
    require_once 'app/library/socket/JSON_socket.php';
    require_once 'app/objects/users/Tokens.php';

    class Admin extends MySQL_socket {

        static private $logger;
        static private $logName;

        static private $token;
        static private $user;
        static private $is_admin;

        static private $getUsersQuery;
        static private $isUserQuery;
        static private $insertUserQuery;
        static private $changeUserTypeQuery;
        static private $toggleLockedQuery;
        static private $deleteUserQuery;

        function __construct($token) {
            parent::__construct();
            self::$token = new Token($token);
            self::$is_admin = false;
            self::$user = self::$token->decodedToken();
            if (self::$user->type === 1) { self::$is_admin = true; }

            self::$logger = new logger();
            self::$logName = '_admin_errorLog';

            self::$getUsersQuery = 'SELECT USERNAME, TYPE, LOCKED FROM USERS';
            self::$isUserQuery = 'SELECT ID FROM USERS WHERE USERNAME = ?';
            self::$insertUserQuery = 'INSERT INTO USERS SET USERNAME = ?, HASH = ?, TYPE = ?';
            self::$changeUserTypeQuery = 'UPDATE USERS SET TYPE = ? WHERE USERNAME = ?';
            self::$toggleLockedQuery = 'UPDATE USERS SET LOCKED = NOT LOCKED WHERE USERNAME = ?';
            self::$deleteUserQuery = 'DELETE FROM USERS WHERE USERNAME = ?';
        }

        function is_admin () { return self::$is_admin; }

        function getAllUsers () {
            if (!self::$is_admin) { return false; }
            $connObj = parent::connect();
            if (!$connObj->error) {
                $connection = $connObj->connection;
                if ($query = $connection->prepare(self::$getUsersQuery)) {
                    $query->execute();
                    $query->bind_result($username, $type, $locked);
                    $users = (array)[];
                    while ($query->fetch()) {
                        if ($username != self::$user->username && $username != 'admin') {
                            $user = (object)[
                                'username' => $username,
                                'type' => $type,
                                'locked' => $locked
                            ];
                            array_push($users, $user);
                        }
                    }
                    $query->close();
                    $connection->close();
                    return $users;
                }
                $connection->close();
            } else { self::databaseError($connObj->connection); }
            return false;
        }

        function createUser ($newUser) {
            if (!self::$is_admin) { return false; }
            if (!property_exists($newUser, 'username') || !property_exists($newUser, 'password') || !property_exists($newUser, 'type')) { return false; }
            if (!self::isUser($newUser->username)) {
                $newUser->hash = password_hash($newUser->password, PASSWORD_DEFAULT);
                $connObj = parent::connect();
                if (!$connObj->error) {
                    $connection = $connObj->connection;
                    if ($query = $connection->prepare(self::$insertUserQuery)) {
                        $query->bind_param('ssi', $newUser->username, $newUser->hash, $newUser->type);
                        $query->execute();
                        $success = $query->affected_rows != -1 ? true : false;
                        $query->close();
                        $connection->close();
                        return $success;
                    } else { $connection->close(); }
                } else { self::databaseError($connObj->connection); }
            }
            return false;
        }

        function changeUserType ($username, $type) {
            if (!self::$is_admin) { return false; }
            if (!is_string($username) || strlen($username) === 0 || $username === self::$user->username || $username === 'admin' || !is_numeric($type)) { return false; }
            if (self::isUser($username)) {
                $connObj = parent::connect();
                if (!$connObj->error) {
                    $connection = $connObj->connection;
                    if ($query = $connection->prepare(self::$changeUserTypeQuery)) {
                        $query->bind_param('is', $type, $username);
                        $query->execute();
                        $success = $query->affected_rows != -1 ? true : false;
                        $query->close();
                        $connection->close();
                        return $success;
                    } else { $connection->close(); }
                } else { self::databaseError($connObj->connection); }
            }
            return false;
        }

        function toggleLockedUser ($username) {
            if (!self::$is_admin) { return false; }
            if (!is_string($username) || strlen($username) === 0 || $username === self::$user->username || $username === 'admin') { return false; }
            if (self::isUser($username)) {
                $connObj = parent::connect();
                if (!$connObj->error) {
                    $connection = $connObj->connection;
                    if ($query = $connection->prepare(self::$toggleLockedQuery)) {
                        $query->bind_param('s', $username);
                        $query->execute();
                        $success = $query->affected_rows != -1 ? true : false;
                        $query->close();
                        $connection->close();
                        return $success;
                    } else { $connection->close(); }
                } else { self::databaseError($connObj->connection); }
            }
            return false;
        }

        function deleteUser ($username) {
            if (!self::$is_admin) { return false; }
            if (!is_string($username) || $username === self::$user->username || $username === 'admin') { return false; }
            if (self::isUser($username)) {
                $connObj = parent::connect();
                if (!$connObj->error) {
                    $connection = $connObj->connection;
                    if ($query = $connection->prepare(self::$deleteUserQuery)) {
                        $query->bind_param('s', $username);
                        $query->execute();
                        $success = $query->affected_rows != -1 ? true : false;
                        $query->close();
                        $connection->close();
                        return $success;
                    } else { $connection->close(); }
                } else { self::databaseError($connObj->connection); }
            }
            return false;
        }

        function updateDBConf ($newConf) {
            if (!self::$is_admin) { return false; }
            //  TODO:   Write the new information to the database config-file.
        }

        function newTokenKey () {
            if (!self::$is_admin) { return false; }

        }

        private function isUser ($username) {
            $connObj = parent::connect();
            if (!$connObj->error) {
                $connection = $connObj->connection;
                if ($query = $connection->prepare(self::$isUserQuery)) {
                    $query->bind_param('s', $username);
                    $query->execute();
                    $query->bind_result($resultId);
                    $query->fetch();
                    return $resultId ? true : false;
                }
            } else { self::databaseError($connObj->connection); }
        }

        private static function databaseError($error) {
            $msg = 'Unable to connect to the database : '.$error;
            self::$logger->log(self::$logName, $msg);
            throw new Exception($msg);
        }

    }
?>