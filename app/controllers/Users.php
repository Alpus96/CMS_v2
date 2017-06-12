<?php
    /**
     *
     */
    class Users extends User_Model
    {
        static private $ActiveUsers;
        private $confirmed;

        public function __construct($key = null)
        {
            parent::__construct();
            $this->ActiveUsers = new ActiveUsers();
            $this->confirmed = false;
            //  TODO:   Authenticate the key and set the active
            //          user to the username of the active user.
        }

        public function authenticate ($username, $password)
        {
            //  TODO:   Confirm username and password match a pair
            //          in the database and generate a key.
        }

        public function changePassword ($newPassword)
        {
            if(!$this->confirmed) { return; }
            //  TODO:   Change the password of the activeUser
            //          to newPassword.
        }

        public function changeUsername ($newUsername)
        {
            if(!$this->confirmed) { return; }
            //  TODO:   Change the username of the activeUser
            //          to newUsername and update active users.
        }

        public function logout ()
        {
            if(!$this->confirmed) { return; }
            //  TODO:   Remove the users active status.
            session_destroy();
            $this->ActiveUsers->remove($this->confirmed->id);
            $this->confirmed = false;
        }
    }
?>