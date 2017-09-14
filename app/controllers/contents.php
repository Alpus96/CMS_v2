<?php
    /*
    *
    * */
    class content extends content_model
    {
        static private $active_user;
        private $authorized;

        function __construct($auth_token)
        {
            parent::__construct();
            $this->active_user = new user_soket();
			$confirmation = $this->active_user->confirm($auth_token)
            $this->authorized = ;
            //  TODO:   Authenticate the key and set the active
            //          user to the username of the active user.
        }
    }
?>