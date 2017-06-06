<?php
    /**
     *
     */
    class Owner_Model extends MySQL_Socket
    {

        protected function __construct($key)
        {
            //  TODO: Authenticate the given key.
        }

        protected function createUser ($username, $password)
        {
            //  TODO:   Add a new user to the database.
        }

        protected function changePW ($username, $newPassword)
        {
            //  TODO:   Change the password of the target user in the database.
        }

        protected function changeUsername ($username, $newUsername)
        {
            //  TODO:   Change the username of the target user in the database.
        }

        protected function deleteUser ($username)
        {
            //  TODO:   Remove the target user from the database.
        }
        
    }
?>