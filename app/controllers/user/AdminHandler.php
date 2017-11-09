<?php

    require_once ROOT_PATH.'app/models/user/AdminModel.php';
    require_once ROOT_PATH.'library/plugin/JWT_Store/TokenStore.php';

    class AdminHandler extends AdminModel {

        static private $token_store;
        static private $config;

        static private $is_admin;
        static private $user;

        function __construct ($token) {
            parent::__construct();
            self::$token_store = new TokenStore();
            self::$user = self::$token_store->verify($token);
            if (!self::$user || self::$user->type != 1) 
            { self::$is_admin = false; }
            else { self::$is_admin = true; }

            if (self::$is_admin) {
                $json_socket = new JsonSocket(ROOT_PATH.'library/json_lib/');
                $conf = $json_socket->read('UsersConfig');
                if (!$conf || !is_object($conf)) {


                } else {

                    $tl_is = property_exists($conf, 'userTypes');

                }

            }
        }

        function addUser ($new_user) {
            if (!self::$is_admin || !is_object($new_user)) { return false; }
            $u_hun = property_exists($new_user, 'username');
            $u_hpw = property_exists($new_user, 'password');
            $u_ht = property_exists($new_user, 'type');
            if (!$u_hun || !$u_hpw || ! $u_ht) { return false; }
            $new_user->hash = password_hash($new_user->password, PASSWORD_DEFAULT);
            unset($new_user->password);
            return parent::insertUser($new_user);
        }

        function getAllUsers () {
            if (self::$is_admin) {
                $users = parent::selectAll();
                foreach ($users as $key => $user) {
                    if ($user->username === self::$user->username || $user->username == 'admin') {
                        array_splice($users, $key, 1);
                    }
                }
                return $users;
            }
            return false;
        }

        function changeType ($username, $type) {
            if (!self::$is_admin) { return false; }
            $is_type = false;
            foreach (self::$config->user_types as $e_type) {
                if ($type == $e_type) {
                    $is_type = true;
                    break;
                }
            }
            if (!$is_type) { return false; }
            return parent::updateType($username, $type);
        }

        function toggleLock ($username) {
            if (!self::$is_admin) { return false; }
            return parent::invertLock($username);
        }

        function deleteUser ($username, $pass) {
            if (!self::$is_admin) { return false; }
            if (!password_verify($pass, self::$user->hash)) { return false; }
            return parent::delete($username);
        }

        function getSettingsPages () {

        }

        //  Handle usersconfuration!

        function updateTokenConf () {}

    }
?>