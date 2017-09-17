<?php
    /**
    *   TODO:   Add SQL-injection reg-ex.
    * */
    class activeUser_socket extends MySQL_socket
    {
        //  Logger class instance varaible and log file name.
        static private $logger;
        static private $logName;

        static private $key;    //  The encoding key for JWT.
        protected $error;       //  The error message variable.

		/*
        *   @description    The construct function handels the initial setup
        *                   of this class, by loading the JWT class and
        *                   confirming the key is usable.
        *
        *   @throws         JWT key not set or could not read error.
		* */
        public function __construct ($key = false)
        {
			// Construct the parent MySQL_socket class.
			parent::__construct();

			//	Initiate the logger class to log errors for debuging purposes.
            $this->logger = new logger();
            $this->logName = '_user_socket_log';

            //  Check if a key was passed as an argument.
            if (!$key)
            {
                //	If no key was passed load the JSON webb token configuration
                //  and get the secret key for encoding.
                $json_socket = new JSON_socket();
    			$jwt_config = $json_socket->read('JWT_config');

                //  Confirm the configuration key was successfully read.
                if ($jwt_config && key($jwt_config) == 'key')
                {
                    //  If the key was read save it to be used for token encryption.
                    $this->key = $jwt_config->key;
                }
                else
                {
                    //  If the key was not read correctly log the problem and throw an error.
                    $msg = 'The JWT token key could not be read or has not been set.';
                    $this->logger->log(
                        $this->logName,
                        $msg
                    );
                    throw new Exeption($msg);
                }
            }
            else
            {
                //  If a key was passed confirm it is a non-empty string.
                if (is_string($key) && $key != '')
                {
                    //  If it was a non-empty string set it as the key.
                    $this->key = $key;
                }
                else
                {
                    //  If it was not a non-empty string log it and throw an error.
                    $msg = 'JWT key must be a non-empty string.';
                    $this->logger->log(
                        $this->logName,
                        $msg
                    );
                    throw new Exeption($msg);
                }
            }

            //  Evauate the strength of the key.
            if (!preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $this->key))
            {
                //  If the key was weak log a warning.
                $this->logger->log(
                    $this->logName,
                    'WARNING : JWT key is weak!'
                );
            }

			//	Load the JSON webb token plugin.
			$this->JWT = new JWT();
		}

		/*
		*	@description    The create function adds a timestap to an object
        *                   and encodes it to a JWT.
		*
		*	@arguments      $object is the object with data
        *                   that will been encoded as a JWT.
        *                   Must have the property 'username' and 'password'.
		*	@returns        The encoded JWT or false on fail.
		* */
        public function create ($object)
        {
            //  Reset the error string.
            $this->error = '';

            //  Confirm the object contains the property 'username'.
			if (!is_object($object) || !property_exists($object, 'username') ||
                !property_exists($object, 'password'))
			{
                //  If the passed argument was not an object or
                //  the required property was not present
                //  log the problem and return fasle.
                $this->error = 'Invalid parameter passed to create active user.';
				return false;
			}

            //  Confirm the username and password is registered in the database.
            if (!$this->confirmUser($object->username, $object->password))
            {
                //  If not log the error and return false.
                $this->error = 'WARNING: Suspected atempt to create token for non-user: '.$this->error;
                $this->logger->log(
                    $this->logName,
                    $this->error
                );
                return false;
            }

            //  Confirm that the user is not already active.
            if ($this->confirmInactive($object->username))
            {
                //  If the user is not currently active add a timestamp
                //  to the object before encoding it.
    			$object->timestamp = time();
    			$token = $this->JWT->encode($object, $this->key);

                //  Connect to the database.
    			$mysql = parent::connect();

                //  Confirm connection was successful.
                if (!$mysql->error)
                {
                    //  If the database connection succeded prepare the query.
                    if ($query = $mysql->connection->prepare(
                        'INSERT INTO ACTIVE_USERS SET USERNAME = ?, TOKEN = ?'))
    				{
                        //  Bind the parameters and perform the query.
    					$query->bind_param('ss', $object->username, $token);
    					$query->execute();

                        //  Close the query and the connection.
    					$query->close();
                        $mysql->connection->close();

                        //  Get the id of the new token entry.
                        $id = $this->getNewId($object->username);

                        //  Confirm the id was successfully aquired.
                        if ($id != null && is_int($id))
                        {
                            //  If the id returned ok return the JWT
                            //  with the id as a property
                            //  with the token as the value.
                            return (object) $JWT->{$id} = $token;
                        }
                        else
                        {
                            //  If the id was not aquired successfully log
                            //  the error adn return false.
                            $this->error = 'Fetching id of new token entry was unsuccessful.';
                            return false;
                        }
                    }
                    else
                    {
                        //  If the prepare of the query failed
                        //  close the database connection and
                        //  set the error before return false.
                        $this->error = 'Unable to create a new token entry: '.$mysql->connection->error;
                        $mysql->connection->close();
                        return false;
                    }
                }
                else
                {
                    //  If connecting to the database was unsuccessful
                    //  log the error and return fasle.
                    $this->error = 'ERROR: Could not connect to the database: '.$mysql->connection->error;
                    $this->logger->log(
                        $this->logName,
                        $this->error
                    );
                    return false;
                }
            }
        }

		/*
		*   @description    This function confirms that the passed token
        *                   is valid and belongs to an active user before
        *                   returning the decoded token and the new token.
        *
        *   @arguments      $JWT : The recieved token.
        *   @throws         Unable to decode JWT.
        *   @returns        False if unsuccessful or
        *                   (object)['decoded_token', 'new_token']
		* */
        public function confirm ($JWT, $internal_call = false)
        {
            //  Reset the error string.
            $this->error = '';

            //  Confirm the JWT.
			if (!$this->isJWT($JWT)) { return false; }

            //  If the JWT passed save the property name as the id.
			$id = intval(key($JWT));

            //  Then get the entry information for that id.
            $active_entry = $this->readActive($id);

            //  Compare the token from the database to the given token.
            if ($active_entry && $active_entry->token === $JWT->$id)
            {
                //  If the tokens match try to decode the one from the database.
                $decoded_token;
                try { $decoded_token = $this->JWT->decode(
                    $active_entry, $this->key, true); }
                catch (Exeption $e)
                {
                    //  If there was an error decoding the token
                    //  log it and return false.
                    $this->error = 'WARNING: Unable to decode JWT: '.$e->errno.' : '.$e;
                    $this->logger->log(
                        $this->logName,
                        $this->error
                    );
                    return false;
                }

                //  If the decoding did not fail
                //  check that the timestamp is from within 10 minuites.
                //  (10*60sec)
                if ($decoded_token->timestamp > time()-(10*60))
                {
                    //  If the time had not expired
                    //  update the timestamp and encode the token again.
                    $decoded_token->timestamp = time();
                    $new_token = $this->encode($decoded_token, $this->key);

                    //  Write the new token to the database.
                    if ($this->updateToken($id, $new_token))
                    {
                        //  If the token was updated successfully
                        //  return the decoded token and the new encoded token.
                        return (object)[
                            'data' => $decoded_token,
                            'new_JWT' => $new_token
                        ];
                    }
                    else
                    {
                        //  If the token could not be updated
                        //  in the database return false.
                        $this->error = 'The token entry could not be updated.';
                        return false;
                    }
                }
                else
                {
                    //  If the timestamp was older than 10 minuites
                    //  check if the call to this function was internal.
                    //  If not call delete else return true.
                    $this->error = 'Expired timestamp.';
                    if (!$internal_call) { $this->delete($JWT); }
                    else { return true; }
                    //  Return false if the call was not intenal.
                    return false;
                }
            }
            else
            {
                //  If the token recieved and the token in the database
                //  for the same id did not match
                //  the token has been altered or is forged.
                //  Log the error and return false.
                $this->error = 'WARNING: Token missmatch! Suspected token forgery.';
                $this->logger->log(
                    $this->logName,
                    $this->error
                );
                return false;
            }
        }

		/*
		*   @description    This function deletes a token entry in the database.
        *
        *   @arguments      $JWT    : The token to delete.
        *   @returns        Boolean : True if successful, false if not.
		* */
        public function delete ($JWT)
        {
            //  Reset the error string.
            $this->error = '';

            //  Confirm the JWT.
            if (!$this->isJWT($JWT)) { return false; }

            //  Confirm the token is valid and belongs to an active user user.
            $valid = $this->confirm($JWT, true);
            if (!$valid)
            {
                //  If it was not valid set the error and return false.
                $this->error = 'Cannot delete invalid entry.';
                return false;
            }

            //  If the token was valid connect to the database.
			$mysql = parent::connect();

            //  Confirm the connection was successful.
            if (!$mysql->error)
            {
                //  If the connection was successful
                //  prepare the query to delete the entry.
                if ($query = $mysql->connection->prepare(
                    'DELETE * FROM ACTIVE_USERS WHERE ID = ? LIMIT 1'))
    			{
                    //  Bind the id as the parameter and run the query.
    				$query->bind_param('i', key($JWT));
    				$query->execute();

                    //  Close the query and the connection.
    				$query->close();
    				$mysql->connection->close();

                    //  Confirm that the query was successful
                    //  and return true if so, false if not.
    				$result = $this->readActive(key($JWT));
    				if (mysql_num_rows($result) == 0) { return true; }
    				else { return false; }
    			}
                else
                {
                    //  If preparing the query was unsuccessful
                    //  close the database connection and set
                    //  the error before returning false.
                    $this->error = 'Unable to delete token entry: '.$mysql->connection->error;
                    $mysql->connection->close();
        			return false;
                }
            }
            else
            {
                //  If the database connection was unsuccessful
                //  log the error and return fasle.
                $this->error = 'ERROR: Could not connect to the database: '.$mysql->connection->error;
                $this->logger->log(
                    $this->logName,
                    $this->error
                );
                return false;
            }
        }

		/*
		*   @description    This function confirms a username and hash
        *                   combination matches a set in the users regestry.
        *
        *   @arguments      $username   : The name of the user.
        *                   $password   : The password hash that should
        *                                 belong to that user.
        *   @returns        Boolean     : True if the set is matching,
        *                                 false if not.
		* */
		private function confirmUser ($username, $password)
		{
            //  Reset the error string.
            $this->error = '';

            //  Confirm the username and password are non-empty strings.
            if (!is_string($username) || $username == '' ||
                !is_string($password) || $password == '')
            {
                $this->error = 'The username and password must be non-empty strings.';
                return false;
            }

            //  If the username and password are non-empty strings
            //  connect to the database.
			$mysql = parent::connect();

            //  Confirm the connection to the database.
            if (!mysql->error)
            {
                //  If the connection was successfull prepare the query
                //  to fetch the hash of the passed username.
                if ($query = $mysql->connection->prepare(
                    'SELECT PASSWORD FROM USERS WHERE USERNAME = ?'))
    			{
                    //  Bind the username as the parameter and run the query.
    				$query->bind_param('s', $username);
    				$query->execute();

                    //  Bind the result and fetch it.
    				$query->bind_result($hash);
    				$query->fetch();

                    //  Close the query and the connection.
    				$query->close();
    				$mysql->connection->close();

                    //  Confirm that the passed password matches
                    //  the hash in the database. If so return true,
                    //  if not return false.
    				if ($password === $hash) { return true; }
    				else { return false; }
    			}
                else
                {
                    //  If the query could not be prepared set the error
                    //  before closing the database connection and return fasle.
                    $this->error = 'Could not confirm user information: '.$mysql->connection->error;
                    $mysql->connection->close();
                    return false;
                }
            }
            else
            {
                //  If the database connection was unsuccessful
                //  log the error and return fasle.
                $this->error = 'ERROR: Could not connect to the database: '.$mysql->connection->error;
                $this->logger->log(
                    $this->logName,
                    $this->error
                );
                return false;
            }
		}

        /*
        *   @description    This function checks if a user
        *                   already has an active token.
        *
        *   @arguments      $username   : The name of the user to look for.
        *   @returns        Boolean     : True if inactive,
        *                                 false if already active.
        * */
        private function confirmInactive($username)
        {
            //  Reset the error string.
            $this->error = '';

            //  Confirm the username is a non-empty string.
            if (!is_string($username) || $username === '')
            {
                //  Set the error and return false
                //  if the username was not a non-empty string.
                this->error = 'Invalid username passed, must be an non-empty string.';
                return false;
            }

            //  Connect to the database.
            $mysql = parent::connect();

            //  Confirm there was no error connecting to the datebase.
            if (!$mysql->error)
            {
                //  If the connection was successful prepare the query.
                if ($query = $mysql->connection->prepare(
                    'SELECT ID FROM ACTIVE_USERS WHERE USERNAME = ?'))
                {
                    //  Bind the username to the query and run it.
                    $query->bind_param('s', $username);
                    $query->execute();

                    //  Bind the result and fetch it.
                    $query->bind_result($id);
                    $query->fetch();

                    //  Close the query and the connection.
                    $query->close();
                    $mysql->connection->close();

                    //  If the query returned no retult
                    //  return true for inactive, otherwise return false.
                    if (mysql_num_rows($id) == 0) { return true; }
                    else { return false; }
                }
                else
                {
                    //  If the query could not be prepared
                    //  set the error, close the connection and return false.
                    $this->error = 'Unable to confirm user inactive: '.$mysql->connection->error;
                    $mysql->connection->close();
                    return false;
                }
            }
            else
            {
                //  If the database connection was unsuccessful
                //  log the error and return fasle.
                $this->error = 'ERROR: Could not connect to the database: '.$mysql->connection->error;
                $this->logger->log(
                    $this->logName,
                    $this->error
                );
                return false;
            }
        }

        /*
        *   @description    This function gets the id of the row
        *                   where the username matches.
        *
        *   @arguments      $username : The name of the user
        *                   to find the id of.
        *   @returns        The id as an integer of false i unsuccessful.
        * */
        private function getNewId($username)
        {
            //  Reset the error string.
            $this->error = '';

            //  Confirm the passed argument is a valid username.
            if (!is_string($username) || $username == '')
            {
                $this->error = 'Invalid username passed, must be a non-empty string.';
                return false;
            }

            //  Connect to the database.
            $mysql = parent::connect();

            //  Confirm there was no connection error.
            if (!$mysql->error)
            {
                //  If connection was successful prepare the query.
                if ($query = $mysql->connection->prepare(
                    'SELECT ID FROM ACTIVE_USERS WHERE USERNAME = ?'))
                {
                    //  Bind the username as the parameter and run the query.
                    $query->bind_param('s', $username);
                    $query->execute();

                    //  Bind the resulting id and fetch it.
                    $query->bind_result($id);
                    $query->fetch();

                    //  Close the query and the connection.
                    $query->close();
                    $mysql->connection->close();

                    //  If the id was fetched return it, if not return false.
                    if (is_numeric($id)) { return $id; }
                    else { return false; }
                }
                else
                {
                    //  If preparing the query was unsuccessful,
                    //  set the error, close the connection and return false.
                    $this->error = 'Unable to get id of new user: '.$mysql->connection->error;
                    $mysql->connection->close();
                    return false;
                }
            }
            else
            {
                //  If the database connection was unsuccessful
                //  log the error and return fasle.
                $this->error = 'ERROR: Could not connect to the database: '.$mysql->connection->error;
                $this->logger->log(
                    $this->logName,
                    $this->error
                );
                return false;
            }
        }

        /*
        *   @description    This function confirms that the argument
        *                   follows the construct of a valid JWT.
        *
        *   @arguments      $JWT    : The token object to confirm.
        *   @returns        Boolean : True if the $JWT has valid construct,
        *                   false if not.
        * */
        private function isJWT($JWT)
        {
            //  Confirm that the JWT is an object with a numeric property
            //  that contains a non-empty string.
			if (!is_object($JWT) || !is_numeric(key($JWT)) ||
                !is_string($JWT->{key($JWT)}) && $JWT->{key($JWT)} === '')
			{
                //  If the passed JWT did not match the specification
                //  log the problem and return false.
                $this->error = 'WARNING: The passed JWT did not match original construct.';
				$this->logger->log(
                    $this->logName,
                    $this->error
                );
				return false;
			}
            //  Return true if it did not fail the check.
            return true;
        }

        /*
		*   @description    This function reads the information at a given id.
        *
        *   @arguments      $id : The id of the row to read.
        *   @returns        (array)['id', 'username', 'token']
		* */
		private function readActive ($id)
		{
            //  Reset the error string.
            $this->error = '';

            //  Confirm the id is a numeric value.
			if (!is_numeric($id))
            {
                //  If the id is not numeric it is invalid.
                $this->error = 'Unable read active user by id with a NaN id.';
				return false;
			}

            //  If the id was numeric cast it to an integer.
			$id = intval($id);

            //  Connect to the database.
			$mysql = parent::connect();

            //  Confirm the connection was successful.
            if (!$mysql->error)
            {
                //  If the connection was successful
                //  prepare the query to read the specified row.
                if ($query = $mysql->connection->prepare(
                    'SELECT * FROM ACTIVE_USERS WHERE ID = ?'))
    			{
                    //  Bind the id as the paramater and run the query.
    				$query->bind_param('i', $id);
    				$query->execute();

                    //  Bind the result and fetch it.
    				$query->bind_result($result);
    				$query->fetch();

                    //  Close the query and the connection
                    //  before returning the result of the query.
    				$query->close();
    				$mysql->connection->close();
    				return $result;
    			}
                else
                {
                    //  If preparing the query was unsuccessful
                    //  set the error and return false.
                    $this->error = 'Unable to query active user by id: '.$mysql->connection->errno;
        			$mysql->connection->close();
        			return false;
                }
            }
            else
            {
                //  If the database connection was unsuccessful
                //  log the error and return fasle.
                $this->error = 'ERROR: Could not connect to the database: '.$mysql->connection->error;
                $this->logger->log(
                    $this->logName,
                    $this->error
                );
                return false;
            }
		}

        /*
        *   @description    This function updates the token of specified id row.
        *
        *   @arguments      $id         : The id of the row to update.
        *                   $new_token  : The new token to input at
        *                                 the specified row.
        * */
        private function updateToken($id, $new_token)
        {
            //  Reset the error string.
            $this->error = '';

            //  Confirm that the id and new token are valid values.
            if (!is_numeric($id) || !is_string($new_token) && $new_token == '')
            {
                $this->error = 'Unable to update token, passed id or token was invalid.';
                return false;
            }

            //  Connect to the database.
            $mysql = parent::connect();

            //  Confirm the connection was successful.
            if (!$mysql->error)
            {
                //  If connection was successful prepare thne query to update the token.
                if ($query = $mysql->connection->pepare(
                    'UPDATE ACTIVE_USERS SET TOKEN = ? WHERE ID = ?'))
                {
                    //  Bind the new token and the id
                    //  to the query and run it.
                    $query->bind_param('si', $new_token, $id);
                    $query->execute();

                    //  Then close the query and the connection.
                    $query->close();
                    $mysql->connection->close();

                    //  Confirm that the query was successful
                    //  by reading the current value.
                    $active_info = $this->readActive($id);

                    //  If the current value matches the new token
                    //  return true, if not return false.
                    if ($active_info->token == $new_token) { return true; }
                    else { return false; }
                }
                else
                {
                    //  If preparing the query was unsuccessful
                    //  set the error, close the connection and return false.
                    $this->error = 'Unable to query token update: '.$mysql->connection->error;
                    $mysql->connection->close();
                    return false;
                }
            }
            else
            {
                //  If the database connection was unsuccessful
                //  log the error and return fasle.
                $this->error = 'ERROR: Could not connect to the database: '.$mysql->connection->error;
                $this->logger->log(
                    $this->logName(),
                    $this->error
                );
                return false;
            }
        }

    }
?>
