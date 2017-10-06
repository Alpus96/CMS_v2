<?php
    require_once 'app/library/socket/MySQL_socket.php';
    require_once 'app/library/socket/JSON_socket.php';
    require_once 'app/library/plugin/jwt/JWT.php';

    class Token extends MySQL_socket {

        static private $logger;
        static private $logName;

        static private $id;
        static private $username;
        static private $token;
        static private $timestamp;

        static private $newTokenQuery;
        static private $getTokenInfoQuery;
        static private $updateTokenQuery;
        static private $deleteTokenQuery;

        public function __construct ($user) {
            self::$newTokenQuery = 'INSERT INTO ACTIVE_USERS SET USERNAME = ?, TOKEN = ?';
            self::$getTokenInfoQuery = 'SELECT ID, TOKEN, TIMESTAMP FROM ACTIVE_USERS WHERE USERNAME = ?';
            self::$updateTokenQuery = 'UPDATE ACTIVE_USERS SET TIMESTAMP = now()';
            self::$deleteTokenQuery = 'DELETE FROM ACTIVE_USERS WHERE ID = ? LIMIT 1';

            self::$logger = new logger();
            self::$logName = 'tokenModel_errorLog';

            if (is_string($user)) {
                $token;
                try {
                    $token = json_decode($user);
                } catch (Exception $e) {
                    $msg = 'Non-JSON string was passed: '.$e;
                    self::$logger->log(self::$logName, $msg);
                    throw new Exception($msg);
                }
                self::updateToken($token);
            }
            else if (is_object($user)) { self::newToken($user); }
        }

        public function deleteToken () {
            if (self::$id) {
                $connObj = parent::connect();
                if (!$connObj->error) {
                    $connection = $connObj->connection;
                    $queryStr = str_replace('?', self::$id, self::$deleteTokenQuery);
                    $connection->query($queryStr);
                    return self::active(self::$username);
                }
            }
            return false;
        }

        public function toJSON () {
            if (isset(self::$id) && isset(self::$username) && isset(self::$token)) {
                $obj = (object) [
                    'id' => self::$id,
                    'username' => self::$username,
                    'token' => self::$token,
                ];
                return json_encode($obj);
            }
            return false;
        }

        public function decodedToken() {
            $JSON = new JSON_socket();
            $key = $JSON->read('JWTConfig')->key;
            $JWT = new JWT($key);
            return $JWT->decode(self::$token, $key);
        }

        private function newToken ($user) {
            if (!property_exists($user, 'username') || !is_string($user->username) || $user->username === '') { return; }
            if (!self::active($user->username)) {
                $JSON = new JSON_socket();
                $key = $JSON->read('JWTConfig')->key;
                $JWT = new JWT($key);
                $token = $JWT->encode($user, $key);
                $connObj = parent::connect();
                if (!$connObj->error) {
                    $connection = $connObj->connection;
                    if ($query = $connection->prepare(self::$newTokenQuery)) {
                        $query->bind_param('ss', $user->username, $token);
                        $query->execute();
                        $query->close();
                        $connection->close();
                        return self::active($user->username);
                    } else { $connection->close(); }
                } else {
                    $msg = 'Unable to connect to the database : '.$connObj->connection;
                    self::$logger->log(self::$logName, $msg);
                    throw new Exception($msg);
                }
            }
        }

        private function active ($username) {
            $connObj = parent::connect();
            if (!$connObj->error) {
                $connection = $connObj->connection;
                if ($query = $connection->prepare(self::$getTokenInfoQuery)) {
                    $query->bind_param('s', $username);
                    $query->execute();
                    $query->bind_result(self::$id, self::$token, self::$timestamp);
                    $query->fetch();
                    $query->close();
                    $connection->close();
                    self::$timestamp = self::$timestamp ? strtotime(self::$timestamp) : null;
                    if (self::$timestamp && self::$timestamp < (time()-(10*60))) {
                        self::deleteToken();
                        self::$timestamp = null;
                    }
                    self::$id = self::$timestamp ? self::$id : null;
                    self::$token = self::$id ? self::$token : null;
                    self::$username = self::$id ? $username : null;
                    return self::$id ? true : false;
                } else { $connection->close();}
            } else {
                $msg = 'Unable to connect to the database : '.$connObj->connection;
                self::$logger->log(self::$logName, $msg);
                throw new Exception($msg);
            }
        }

        private function updateToken ($token) {
            if (!property_exists($token, 'id') || !property_exists($token, 'username') || !property_exists($token, 'token')) { return; }
            if (self::active($token->username)) {
                $connObj = parent::connect();
                if (!$connObj->error) {
                    $connection = $connObj->connection;
                    if ($token->id === self::$id && $token->token === self::$token) {
                        self::$username = $token->username;
                        if (!$connection->query(self::$updateTokenQuery)) {
                            self::$logger->log(self::$logName, 'Unable to update the token timestamp : '.$connObj->connection);
                        }
                    }
                } else {
                    $msg = 'Unable to connect to the database : '.$connObj->connection;
                    self::$logger->log(self::$logName, $msg);
                    throw new Exception($msg);
                }
                $connection->close();
            }
        }

    }
?>