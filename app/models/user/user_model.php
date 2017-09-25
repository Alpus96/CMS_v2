<?php
    class User_Model extends MySQL_Socket
    {
        static private $logger;
        static private $logName;
        /**
        *   @desc   This function is run when a new instance of
        *           this class is created, but only within a child
        *           class. It creates an instance of the parent
        *           class within itself.
        */
        protected function __construct()
        {
            parent::__construct();
            self::$logger = new logger();
            self::$logName = '_userModel_errorLog';
        }

        /**
        *   @desc   This function gets the information of a user in
        *           the database if the given username is registered.
        */
        protected function getByUsername ($username)
        {
            //  Get the database connection from the parent.
            $mysql = parent::connect();

            if (!$mysql->error)
            {
                //  Prepare the query.
                if ($query = $mysql->connection->prepare('SELECT * FROM USERS WHERE USERNAME = ? LIMIT 1'))
                {
                    //  Bind the passed username to the query and run it.
                    $query->bind_param('s', $username);
                    $query->execute();

                    //  Fetch the result.
                    $query->bind_result($id, $name, $hash, $type);
                    while ($query->fetch())
                    {
                        $user = (object)[
                            'id' => $id,
                            'username' => $name,
                            'hash' => $hash,
                            'type' => $type
                        ];
                    }

                    //  Close the query and the connection before returning
                    //  the result.
                    $query->close();
                    $mysql->connection->close();
                    return $user ? $user : false;
                } else {
                    self::$logger->log(
                        self::$logName,
                        'unable to prepare query.'
                    );
                    return false;
                }
            }
            else
            {
                //  If preparing the query was unsuccessful close the connection
                //  and return false.
                //$mysql->connection->close();
                return false;
            }
        }

        /**
        *   @desc   This function changes the password for the specified
        *           user to the given password.
        */
        protected function changePassword ($activeUser, $newPassword)
        {
            //  Get the database connection from the parent.
            $connection = parent::connect();
            //  Prepare the query.
            if ($query = $connection->prepare('UPDATE users SET password = ? WHERE username = ?'))
            {
                //  Bind the new password and the given username to the
                //  query and run it.
                $query->bind_param('ss', $newPassword, $activeUser);
                $query->execute();

                //  Fetch the result.
                //  NOTE: Uneccessary? Could be true or false?
                $query->bind_result($success);
                $query->fetch();

                //  Close the query and the database connection before
                //  retuning the result.
                $query->close();
                $connection->close();
                return $success;
            }
            //  If preparing the query was unsuccessful close the
            //  connection and return false.
            $connection->close();
            return false;
        }

        /**
        *   @desc   This function chnges the username of the
        *           specified user to the given username.
        */
        protected function changeUsername ($activeUser, $newUsername)
        {
            //  Get the database connection from the parent.
            $connection = parent::connect();
            //  Prepare the query.
            if ($query = $connection->prepare('UPDATE users SET username = ? WHERE username = ?'))
            {
                //  Bind the new username and the old username to
                //  the query and run it.
                $query->bind_param('ss', $newUsername, $activeUser);
                $query->execute();

                //  Fetch the result.
                //  NOTE: Uneccessary? Could be true or false?
                $query->bind_result($success);
                $query->fetch();

                //  Close the query and the connection before returning
                //  the result.
                $query->close();
                $connection->close();
                return $success;
            }
            //  If preparing the query was unsuccessful close the connection
            //  and return false.
            $connection->close();
            return false;
        }

    }
?>
