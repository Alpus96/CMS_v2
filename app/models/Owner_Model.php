<?php
    /**
     *
     */
    class Owner_Model extends MySQL_Socket
    {

        protected function __construct()
        { parent::__construct(); }

        protected function getAllUsers ()
        {
            //  TODO: Get and return all usernames in the database.
        }

        protected function createUser ($username, $password, $type)
        {
            //  TODO:   Add a new user to the database.
        }

        protected function toggleLockedUser ($target)
        {
            //  TODO: Lock or unlock target depending on previus state.
        }

        protected function changeUserType ($target, $type)
        {
            //  TODO: Update the type field of target.
        }

        protected function deleteUser ($target)
        {
            //  TODO:   Remove the target user from the database.
        }

    }
?>