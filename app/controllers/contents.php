<?php
    /**
     *
     */
    class Content_Manager extends Content_Model
    {
        static private $ActiveUsers;
        private $confirmed;

        function __construct(argument)
        {
            parent::__construct();
            $this->ActiveUsers = new ActiveUsers();
            $this->confirmed = false;
            //  TODO:   Authenticate the key and set the active
            //          user to the username of the active user.
        }
    }
?>