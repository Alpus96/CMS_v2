<?php
    class User extends UserModel
    {

        static private $logger;
        static private $logName;

        function __construct ($identifier = null) {
            parent::__construct($identifier);

            self::$logger = new logger();
            self::$logName = '_user_log';
        }

        function authenticate ($password) {
            $token = false;
            $user = parent::toObject();
            if ($user && password_verify($password, $user->hash)) {
                $JWT_handler = new activeUser_socket();
                $token = $JWT_handler->create($user);
            }
            return $token;
        }

        function is_active ($token) {
            if (!$token || !property_exists($token, 'id') || !property_exists($token, 'token') || !property_exists($token, 'timestamp')) {
                header('location: /projects/CMS_v2/login');
                return false;
            }

            $token = new Token($token->id, $token->token, $token->timestamp);

            $is_active = false;
            if ($token)
            {
                $active_users = new activeUser_socket();
                $is_active = $active_users->confirm($token);
            }

            if ($token && $is_active) {
                return true;
            } else {
                header('location: /projects/CMS_v2/login');
                return false;
            }
        }

        function logout ($token) {
            $token = new Token($token->id, $token->token, $token->timestamp);
            $active_user = new activeUser_socket();
            return $active_user->delete($token);
        }

    }
?>
