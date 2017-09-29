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

        static function setToken ($new_token) { self::$token = $new_token; }

        static function getToken () { return self::$token; }

        static function updateTimestamp () { self::$timestamp = time(); }

        static function getTimestamp () { return self::$timestamp; }

        static function fromJSON ($json)
        {
            if (!is_string($json)) { return false; }

            $json_obj;
            try { $json_obj = json_decode($json); }
            catch (Exeption $e) { throw new Exeption('Unable to parse json string to Token.'); }

            if (property_exists('id') && property_exists('token') && property_exists('timestamp'))
            {
                if (is_numeric($json_obj->id) && is_string($json_obj->token) && $json_obj->token !== '' && is_numeric($json_obj->timestamp))
                {
                    self::$id = $json_obj->id;
                    self::$token = $json_obj->token;
                    self::$timestamp = $json_obj->timestamp;

                    return true;
                }
            }
            return false;
        }

        static function toJSON ()
        {
            return json_encode((object)[
                'id' => self::$id,
                'token' => self::$token,
                'timestamp' => self::$timestamp
            ]);
        }

        static function sameAs ($other_token)
        {
            $ot = $other_token;
            if ($other_token instanceof Token)
            {
                if (self::$id === $ot->getId() && self::$token === $ot->getToken() && self::$timestamp === $ot->getTimestamp()) { return true; }
            }
            return false;
        }

    }
?>
