<?php
    /**
    *   TODO:   Review code and comments.
    *
    *   TODO:   Convert into using JWT.
    *
    *   NOTE:   How to send cridentials securely without HTTPS / SSL.
    */
    class Users extends User_Model
    {
        private $confirmed;

        /**
        *
        */
        public function __construct($key = false)
        {
            //  Construct the parent class.
            parent::__construct();
            //  Authenticate the given key.
            $this->confirmed = false;
            if ($key) { $this->authKey($key); }
        }

        /**
        *   @desc   Authenticate the key and set the active user
        *           to the username of the active user.
        */
        private function authKey($key)
        {
            //  Create a new instance of the active users class.
            $ActiveUsers = new ActiveUsers();
            //  Fetch information linked to the given key.
            $user = $ActiveUsers->fetch($key);
            //  Confirm that there was any information and the
            //  ip of the request is the same.
            //  NOTE:   If this is not so the value of confirmed
            //          will remain false by default.
            if ($user && $user->ip === $_SERVER['REMOTE_ADDR'])
            {
                //  Save the liked username as the confirmed user.
                $this->confirmed = $user->username;
            }
        }

        /**
        *   @desc   This function generates a random secure string
        *           as long as $length.
        */
        private function genKey ($length)
        {
            //  Create the variable to hold the generated hash.
            $keyValue = '';

            //  Add the hash(es) to the variable until long enough.
            for ($i = 0; $i < ceil($length/ 40); $i++)
            { $keyValue .= sha1(microtime(true).mt_rand(10000, 90000)); }

            //  Return a string value as long as $length based on the hashes.
            return substr($keyValue, 0, $length);
        }

        /**
        *   @desc   Confirms that the given username and password
        *           match a pair in the database, if so generates
        *           a key and saves it with the username and the
        *           remote ip.
        */
        public function authenticate ($username, $password)
        {
            //  Gets the user from the database with the given username.
            $user = parent::getUserByUsername($username);
            //  confirms that a user was found and that the passwords can be matched.
            if ($user && password_verify($password, $user->password))
            {
                return true;
                //  If the cridentials where valid generates a key.
                //  When the key has been generated saves it together with the username.
                //  NOTE:   How to know key is not stolen?
                //$ActiveUsers = new ActiveUsers();
                //return $ActiveUsers->add($user->username, $this->genKey(256));
            }
            //  If a user was not found or the passwords did not match return false.
            return false;
        }

        /**
        *   @desc   Changes the password of the active user to the new password.
        */
        public function changePassword ($newPassword)
        {
            //  If there is no active user or the key could not be confirmed return nothing.
            if(!$this->confirmed) { return false; }
            //  If there was an active user change the password to the given password and return the result.
            else
            {
                //  Try hashing the given password.
                if ($hash = password_hash($newPassword, PASSWORD_DEFAULT))
                {
                    //  Change the active users password to the new hash and return the result.
                    return parent::changePassword($this->confirmed, $hash);
                }
            }
            //  If hashing the given password failed return false.
            return false;
        }

        /**
        *   @desc   Change the username of the active user to the given new username and update active users.
        */
        public function changeUsername ($newUsername)
        {
            //  If there is no active user or the key could not be confirmed return nothing.
            if(!$this->confirmed) { return false; }
            //  If there was an active user change the username to the given username.
            else {
                if ( parent::changeUsername($this->confirmed, $newUsername) )
                {
                    // If changing the username was successful update the active user and return true.
                    $ActiveUsers = new ActiveUsers();
                    $ActiveUsers->update($this->confirmed, $newUsername);
                    $this->confirmed = $newUsername;
                    return true;
                }
            }
            //  If changing the username failed return false.
            return false;
        }

        /**
        *   @desc   Remove the users active status.
        */
        public function logout ()
        {
            //  If the user is not active return false.
            if(!$this->confirmed) { return false; }
            //  NOTE: Is 'session_destroy();' nessesary?
            //  Remove the user from active users.
            $this->ActiveUsers->remove($this->confirmed);
            //  Set confirmed active to false for this instance.
            $this->confirmed = false;
            //  Return true.
            return true;
        }

    }
?>
