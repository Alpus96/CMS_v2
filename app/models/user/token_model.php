<?php
    class Token
    {
        static private $id;
        static private $token;
        static private $timestamp;

        function __construct ($active_id, $token_string, $time = null)
        {
            if (!is_numeric($active_id)||!is_string($token_string)||$token_string === '')
            { throw new Exeption('Invalid token id or token string.'); }
            self::$id = $active_id;
            self::$token = $token_string;
            self::$timestamp = $time ? $time : time();
        }

        static function getId () { return self::$id; }

        static function setToken($new_token) { self::$token = $new_token; }

        static function getToken () { return self::$token; }

        static function updateTimestamp () { self::$timestamp = time(); }

        static function getTimestamp () { return self::$timestamp; }

        static function toJSON ()
        {
            return json_encode((object)[
                'id' => self::$id,
                'token' => self::$token,
                'timestamp' => self::$timestamp
            ]);
        }
    }
?>
