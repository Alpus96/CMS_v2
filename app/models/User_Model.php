<?php
    /**
     *  TODO: Write comments.
     */
    class Owner_Model extends MySQL_Socket
    {

        protected function __construct()
        { parent::__construct(); }

        protected function getUserByUsername ($username)
        {
            $connection = parent::connect();
            if ($query = $connection->prepare('select * from users table where username = ?'))
            {
                $query->bind_param('s', $username);
                $query->execute();

                $query->bind_result($user);
                $query->fetch();

                $query->close();
                $connection->close();
                return $user;
            }
            $connection->close();
            return false;
        }

        protected function changePassword ($newPassword)
        {
            $connection = parent::connect();
            if ($query = $connection->prepare('update users set password = ? where username = ?'))
            {
                $query->bind_param('ss', $newPassword, $this->activeUser);
                $query->execute();

                $query->bind_result($success);
                $query->fetch();

                $query->close();
                $connection->close();
                return $success;
            }
            $connection->close();
            return false;
        }

        protected function changeUsername ($newUsername)
        {
            $connection = parent::connect();
            if ($query = $connection->prepare('update users set username = ? where username = ?'))
            {
                $query->bind_param('ss', $newUsername, $this->activeUser);
                $query->execute();

                $query->bind_result($success);
                $query->fetch();

                $query->close();
                $connection->close();

                if ($success)
                {
                    $this->activeUser = $newUsername;
                }

                return $success;
            }
            $connection->close();
            return false;
        }

    }
?>