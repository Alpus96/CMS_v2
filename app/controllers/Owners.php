<?php
    /**
     *
     */
    class Owners extends Owner_model
    {
        static private $ActiveUsers;
        private $confirmed;

        function __construct($key)
        {
            parent::__construct();
            $this->ActiveUsers = new ActiveUsers();
            $this->confirmed = false;
            //  TODO:   Authenticate the key and set the active
            //          user to the username of the active user.
        }

        function createUser ($username, $password, $type)
        {
            if(!$this->confirmed) { return; }
            //  TODO:   Add a new user to the database with the given information.
        }

        function toggleLockedUser ($target)
        {
            if(!$this->confirmed) { return; }
            //  TODO:   Invert the locked status of the target user.
        }

        function changeUserType ($target)
        {
            if(!$this->confirmed) { return; }
            //  TODO:   Change the usertype of the target user.
        }

        function deleteUser ($target)
        {
            if(!$this->confirmed) { return; }
            //  TODO:   Remove the target user from the database.
        }

        function updateDBConf ($newConf)
        {
            if(!$this->confirmed) { return; }
            //  TODO:   Write the new information to the database config-file.
        }
    }
?>