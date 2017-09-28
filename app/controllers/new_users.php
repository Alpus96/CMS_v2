<?php
    class User extends User_Model
    {

        static private $logger;
        static private $logName;

        function __construct ()
        {
            parent::__construct();
            self::$logger = new logger();
            self::$logName = '_userLog';
        }

        function authenticate ($cridentials)
        {
            //  TODO: Verify $cridentials parameters and escape SQL-injection.
            $user = parent::getByUsername($cridentials->username);
            $hash = $user ? $user->hash : '';
            if (password_verify($cridentials->password, $hash))
            { return $user; }
            else { return false; }
        }

    }
?>
