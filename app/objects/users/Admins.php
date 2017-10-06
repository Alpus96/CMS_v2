<?php
    require_once 'app/library/socket/MySQL_socket.php';
    require_once 'app/library/socket/JSON_socket.php';
    require_once 'app/objects/users/Tokens.php';

    class Admin extends MySQL_socket {

        static private $token;
        static private $user;
        static private $is_admin;

        static private $insertUserQuery;
        static private $changePassQuery;
        static private $toggleLockedQuery;
        static private $deleteUserQuery;

        function __construct($token) {
            self::$token = new Token($token);
            self::$is_admin = false;
            self::$user = self::$token->decodedToken();
            if (self::$user->type === 1) { self::$is_admin = true; }

            self::$insertUserQuery = 'INSERT INTO USERS SET USERNAME = ?, HASH = ?, TYPE = ?';
            self::$changePassQuery = 'UPDATE USERS SET HASH = ? WHERE USERNAME = ?';
            self::$toggleLockedQuery = 'UPDATE USERS SET LOCKED = NOT LOCKED WHERE USERNAME = ?';
            self::$deleteUserQuery = 'DELETE * FROM USERS WHERE USERNAME = ?';
        }

        function is_admin () { return self::$is_admin; }

        function createUser ($newUser) {
            if (!self::$is_admin) { return false; }
            if (!property_exists($newUser, 'username') || !property_exists($newUser, 'passowrd') || !property_exists($newUser, 'type')) { return false; }
            $newUser->hash = password_hash($newUser->password, PASSWORD_DEFAULT);
            $connObj = parent::connect();
            if (!$connObj->error) {
                $connection = $connObj->connection;
                if ($query = $connection->prepare(self::$insertUserQuery)) {
                    $query->bind_param('ssi', $newUser->username, $newUser->hash, $newUser->type);
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
            return false;
        }

        function changeUserPass ($username, $newPassword) {
            if (!self::$is_admin) { return false; }
            if (!is_string($username) || $username === '' ||!is_string($newPassword) || $newPassword === '') { return false; }
            $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $connObj = parent::connect();
            if (!$connObj->error) {
                $connection = $connObj->connection;
                if ($query = $connection->prepare(self::$changePassQuery)) {
                    $query->bind_param('ss', $username, $newPassword);
                    $query->execute();
                    return $query->affected_rows > 0 ? true : false;
                } else { $connection->close(); }
            } else {
                $msg = 'Unable to connect to the database : '.$connObj->connection;
                self::$logger->log(self::$logName, $msg);
                throw new Exception($msg);
            }
            return false;
        }

        function toggleLockedUser ($username) {
            if (!self::$is_admin) { return false; }
            if (!is_string($username) || strlen($username) === 0) { return false; }
            $connObj = parent::connect();
            if (!$connObj->error) {
                $connection = $connObj->connection;
                if ($query = $connection->prepare(self::$toggleLockedQuery)) {
                    $query->bind_param('s', $username);
                    $query->execute();
                    return $query->affected_rows > 0 ? true : false;
                } else { $connection->close(); }
            } else {
                $msg = 'Unable to connect to the database : '.$connObj->connection;
                self::$logger->log(self::$logName, $msg);
                throw new Exception($msg);
            }
            return false;
        }

        function changeUserType ($username, $type) {
            if (!self::$is_admin) { return false; }
            if (!is_string($username) || !is_numeric($type)) { return false; }
            $connObj = parent::connect();
            if (!$connObj->error) {
                $connection = $connObj->connection;
                if ($query = $connection->prepare(self::$toggleLockedQuery)) {
                    $query->bind_param('si', $username, $type);
                    $query->execute();
                    return $query->affected_rows > 0 ? true : false;
                } else { $connection->close(); }
            } else {
                $msg = 'Unable to connect to the database : '.$connObj->connection;
                self::$logger->log(self::$logName, $msg);
                throw new Exception($msg);
            }
            return false;
        }

        function deleteUser ($username) {
            if (!self::$is_admin) { return false; }
            if (!is_string($username)) { return false; }
            $connObj = parent::connect();
            if (!$connObj->error) {
                $connection = $connObj->connection;
                if ($query = $connection->prepare(self::$deleteUserQuery)) {
                    $query->bind_param('s', $username);
                    $query->execute();
                    return $query->affected_rows > 0 ? true : false;
                } else { $connection->close(); }
            } else {
                $msg = 'Unable to connect to the database : '.$connObj->connection;
                self::$logger->log(self::$logName, $msg);
                throw new Exception($msg);
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

    }
?>