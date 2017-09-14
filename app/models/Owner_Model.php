<?php
    /**
     *  TODO: Revew code.
     */
    class Owner_Model extends MySQL_Socket
    {
        /**
        *   @desc   The __construct function is run when an instance
        *           of this class is made. It initiates an instance
        *           of the parent class.
        */
        protected function __construct()
        { parent::__construct(); }

        /**
        *   @desc   This function returns the username, locked status
        *           and user type of all users in the database.
        */
        protected function getAllUsers ()
        {
            //  Connect to the database using the parent class.
            $connection = parent::connect();
            //  Try preparing the query.
            //  NOTE: No need to prepare, static query!
            if ($query = $connection->prepare('SELECT username, locked, userType FROM users'))
            {
                //  Run the query.
                $query->execute();

                //  Fecth the data response from the database.
                $query->bind_result($users);
                $query->fetch();

                //  Close the query and connection.
                $query->close();
                $connection->close();

                //  Return the information about the users.
                return $users;
            }
            //  If preparing the query was not successful close
            //  the connection and return false.
            $connection->close();
            return false;
        }

        /**
        *   @desc   This function adds a user to the database.
        */
        protected function createUser ($username, $password, $type)
        {
            //  Connect to the database using the parent class.
            $connection = parent::connect();
            //  Try preparing the query.
            if ($query = $connection->prepare('INSERT INTO users SET ?'))
            {
                //  When the query has been prepared bind the data to it.
                $query->bind_param(
                    'o',
                    (object) [
                        'username' => $username,
                        'password' => $password,
                        'userType' => $type,
                        'locked' => false
                    ]
                );  //  NOTE:   Which datatype to specify?
                //  Then run the query.
                $query->execute();

                //  Get the success reponse from the database.
                $query->bind_result($success);
                $query->fetch();

                //  Close the query and the connection and
                //  return the success response.
                $query->close();
                $connection->close();
                return $success;
            }
            //  If preparing the query was unsuccessful close
            //  the connection and return false.
            $connection->close();
            return false;
        }

        /**
        *   @desc   This function reveses the locked status of a user.
        */
        protected function toggleLockedUser ($target)
        {
            //  Connect to the database using the parent class.
            $connection = parent::connect();
            //  Try preparing the query.
            if ($query = $connection->prepare('SELECT locked FROM users WHERE username = ?'))
            {
                //  Then bind the data to the query and run it.
                $query->bind_param('s', $target);
                $query->execute();

                //  Fetch the data response from the database.
                $query->bind_result($status);
                $query->fetch();

                //  Close the query.
                $query->close();

                //  Confirm that the resonse was a boolean value and the
                //  next query can be prepared.
                if (is_bool($status) && $query = $connection->prepare('UPDATE users (locked) VALUES(?) WHERE username = ?'))
                {
                    //  Bind the data to the new query and run it.
                    $query->bind_param('bs', !$status, $target);
                    $query->execute();

                    //  Fetch the success reponse from the database.
                    $query->bind_result($success);
                    $query->fetch();

                    //  Close the query and the database connection.
                    $query->close();
                    $connection->close();

                    //  Then return the success response.
                    return $success;
                }
            }
            //  If a query failed preparing close
            //  the connection and return false.
            $connection->close();
            return false;
        }

        /**
        *   @desc   This function updates the type of a user.
        */
        protected function changeUserType ($target, $type)
        {
            //  Connect to the database using the parent class.
            $connection = parent::connect();
            //  Try preparing the query.
            if ($query = $connection->prepare('UPDATE users (userType) VALUES(?) WHERE username = ?'))
            {
                //  Then bind the data to the query and run it.
                $query->bind_param('is', $type, $target);
                $query->execute();

                //  Fetch the success reponse from the database.
                $query->bind_result($success);
                $query->fetch();

                //  Close the query and the connection to the database.
                $query->close();
                $connection->close();
                //  Then return the success response.
                return $success;
            }
            //  If the query failed to prepare close
            //  the connection and return false.
            $connection->close();
            return false;
        }

        /**
        *   @desc   This function removes a user from the database.
        */
        protected function deleteUser ($target)
        {
            //  Connect to the database using the parent class.
            $connection = parent::connect();
            //  Try preparing the query.
            if ($query = $connection->prepare('DELETE FROM users WHERE username = ?'))
            {
                //  Then bind the name of the target to the query and run it.
                $query->bind_param('s', $target);
                $query->execute();

                //  Fetch the success response from the database.
                $query->bind_result($success);
                $query->fetch();

                //  Close the query and the connection to the database.
                $query->close();
                $connection->close();

                //  Then return the success response.
                return $success;
            }
            //  If the query failed to prepare close the
            //  connection to the database and return false.
            $connection->close();
            return false;
        }

    }
?>