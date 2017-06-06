<?php
    /**
     *
     */
    class Owner_Model extends MySQL_Socket
    {

        public function __construct($key)
        {
            //  TODO: Authenticate the given key.
        }

        public function authenticate ($username, $password)
        {
            //  TODO:   Confirm the username and password is a matching pair  in the database.
        }

        public function changePW ($newPassword)
        {
            //  TODO:   Change the active users password in the database.
        }

        public function changeUsername ($username, $newUsername)
        {
            //  TODO:   Change the active users username in the database.
        }

    }
?>