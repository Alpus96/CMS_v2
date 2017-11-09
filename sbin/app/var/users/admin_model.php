<?php

    require_once ROOT_PATH.'sbin/lib/opt/socket/mysql_socket.php';
    require_once ROOT_PATH.'sbin/lib/srv/logger.php';

    class AdminModel extends MysqlSocket {

        static private $query;

        protected function __construct () {
            parent::__construct();
            self::$query = (object)[
                'select_all' => 'SELECT USERNAME, TYPE, LOCKED FROM USERS',
                'select' => 'SELECT ID FROM USERS WHERE USERNAME = ?',
                'insert' => 'INSERT INTO USERS SET USERNAME = ?, HASH = ?, TYPE = ?',
                'ud_type' => 'UPDATE USERS SET TYPE = ? WHERE USERNAME = ?',
                'toggle_lock' => 'UPDATE USERS SET LOCKED = NOT LOCKED WHERE USERNAME = ?',
                'delete' => 'DELETE FROM USERS WHERE USERNAME = ?'
            ];
        }

        protected function insertUser ($user) {
            if (!is_object($user)) { return false; }
            $u_e = property_exists($user, 'username');
            $h_e = property_exists($user, 'hash');
            $t_e = property_exists($user, 'type');
            if (!$u_e || !$h_e || !$t_e) { return false; }

            $conn = parent::connect();
            if (!$conn) { return false; }
            if ($query = $conn->prepare(self::$query->insert)) {
                $query->bind_param('ssi', $user->username, $user->hash, $user->type);
                $query->execute();
                $success = $query->affected_rows > 0 ? true : false;
                $query->close();
                $conn->close();
                return $success;
            }
            $conn->close();
            return false;
        }

        protected function selectAll () {
            $conn = parent::connect();
            if (!$conn) { return false; }
            if ($query = $conn->prepare(self::$query->select_all)) {
                $query->execute();
                $query->bind_result($username, $type, $locked);
                $data = [];
                while ($query->fetch()) {
                    $user = (object)[
                        'username' => $username,
                        'type' => $type,
                        'locked' => $locked
                    ];
                    array_push($data, $user);
                }
                $query->close();
                $conn->close();
                return $data;
            }
            $conn->close();
            return false;
        }

        protected function updateType ($username, $type) {
            $u_is = is_string($username);
            $t_ii = is_integer($type);
            if (!$u_is || !$t_ii) { return false; }
            $conn = parent::connect();
            if (!$conn) { return false; }
            if ($query = $conn->prepare(self::$query->ud_type)) {
                $query->bind_param('is', $type, $username);
                $query->execute();
                $success = $query->affected_rows > 0 ? true : false;
                $query->close();
                $conn->close();
                return $success;
            }
            $conn->close();
            return false;
        }

        protected function invertLock ($username) {
            if (!is_string($username)) { return false; }
            $conn = parent::connect();
            if (!$conn) { return false; }
            if ($query = $conn->prepare(self::$query->toggle_lock)) {
                $query->bind_param('s', $username);
                $query->execute();
                $success = $query->affected_rows > 0 ? true : false;
                $query->close();
                $conn->close();
                return $success;
            }
            $conn->close();
            return false;
        }

        protected function delete ($username) {
            if (!is_string($username)) { return false; }
            $conn = parent::connect();
            if (!$conn) { return false; }
            if ($query = $conn->prepare(self::$query->delete)) {
                $query->bind_param('s', $username);
                $query->execute();
                $success = $query->affected_rows > 0 ? true : false;
                $query->close();
                $conn->close();
                return $success;
            }
            $conn->close();
            return false;
        }

    }
?>