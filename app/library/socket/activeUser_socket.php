<?php
    /**
    *   TODO:   Write comments.
    *   TODO:   Review code.
    *   TODO:   Add error handeling for mysql errors.
    *   TODO:   Confirm function to long, try to break up into smaller functions.
    * */
    class activeUser_socket extends MySQL_socket
    {
        //
        static private $logger;
        static private $logName;

        static private $key;    //
        protected $error;       //

		/*
        *   @description    The construct function handels the initial setup
        *                   of this class, by loading the JWT class and
        *                   confirming the key is usable.
        *
        *   @throws         JWT key not set or could not read error.
        *
        *   TODO:   Extra function to specify a key on initiation. (Instead of configured key)
		* */
        public function __construct ()
        {
			// Construct the parent MySQL_socket class.
			parent::__construct();

			//	Initiate the logger class to log errors for debuging purposes.
            $this->logger = new logger();
            $this->logName = '_user_socket_log';

			//	Load the JSON webb token configuration and get the secret key for encoding.
			$json_socket = new JSON_socket();
			$jwt_config = $json_socket->read('JWT_config');

            //  Confirm the configuration key was successfully read.
            if ($jwt_config && key($jwt_config) == 'key')
            {
                //  If the key was read save it to be used for token encryption.
                $this->key = $jwt_config->key;
                //  Evauate the strength of the key.
                if (!preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $this->key))
                {
                    //  If the key was weak log a warning.
                    $this->logger->log(
                        $this->logName,
                        'WARNING : JWT key is weak!'
                    );
                }
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

			//	Load the JSON webb token plugin.
			require_once '../plugin/jwt/JWT.php';
			$this->JWT = new JWT();
		}

		/*
		*	@description    The create function adds a timestap to an object
        *                   and encodes it to a JWT.
		*
		*	@arguments      $object is the object with data that will be encoded as a JWT.
		*	@returns        The encoded JWT or false on fail.
		* */
        public function create ($object)
        {
            //  Confirm the object contains
			if (!is_object($object) || !array_keys((array)$object) === ['type', 'username', 'hash'])
			{
                $msg = 'Invalid parameter passed to create active user.';
				$this->logger->log(
                    $this->logName,
                    $msg
                );
                $this->error = $msg;
				return false;
			}

			$object->timestamp = time();
			$token = $this->JWT->encode($object, $this->key);

			$mysql = parent::connect();

            if (!$mysql->error)
            {
                if ($query = $mysql->connection->prepare('SELECT ID FROM ACTIVE_USERS WHERE USERNAME = ?'))
    			{
    				$query->bind_param('s', $object->username);
    				$query->execute();

    				$query->bind_result($active_id);
    				$query->fetch();

    				$query->close();

    				if (mysql_num_rows($active_id) == 0 && $query = $mysql->connection->prepare('INSERT INTO ACTIVE_USERS SET USERNAME = ?, TOKEN = ?'))
    				{
    					$query->bind_param('ss', $object->username, $token);
    					$query->execute();
    					$query->close();

    					if ($query = $mysql->connection->prepare('SELECT ID FROM ACTIVE_USERS WHERE USERNAME = ?'))
    					{
    						$query->bind_param('s', $object->username);
    						$query->execute();

    						$query->bind_result($id);
    						$query->fetch();

    						$query->close();
    						$mysql->connection->close();

    						if ($id != null && is_int($id)) { return (object) $JWT->{$id} = $token; }
    						else
    						{
    							$this->logger->log(
                                    $this->logName,
                                    'Fetching id of new token entry went wrong: '.$connetion->errno
                                );
    							return false;
    						}
    					}
    					$this->logger->log(
                            $this->logName,
                            'Unable to read id of new token entry: '.$connection->errno
                        );
    					$mysql->connection->close();
    					return false;
    				}
    			}
    			$this->logger->log(
                    $this->logName,
                    'Unable to make new token query: '.$connection->errno
                );
    			$mysql->connection->close();
    			return false;
            }
        }

		/*
		*
		* */
        public function confirm ($JWT)
        {
			if (!is_numeric(key($JWT)))
			{
				$this->logger->log($this->logName, 'Unable to confirm JWT token without original id.');
				return false;
			}
			$id = intval(key($JWT));

            $mysql = parent::connect();

            if (!$mysql->error)
            {
                if ($qurey = $mysql->connection->prepare('SELECT TOKEN FROM ACTIVE_USERS WHERE ID = ? LIMIT 1'))
                {
                    $query->bind_param('i', $id);
                    $query->execute();

                    $query->bind_result($token);
                    $query->fetch();

                    $query->close();

    				if ($JWT->$id == $token)
    				{
    					try
    					{
    						$decoded_token = $this->JWT->decode($token, $this->key, true);

    						if ($decoded_token->timestamp > time()-(10*60))
    						{
    							if ($this->confirmUser($decoded_token->username, $decoded_token->hash)
    							{
    								$decoded_token->timestamp = time();
    								$new_token = $this->JWT->encode($decoded_token, $this->key, true);

    								if ($query = $mysql->connection->prepare('UPDATE ACTIVE_USERS SET TOKEN = ? WHERE USERNAME = ?'))
    								{
    									$query->bind_param('ss', $new_token, $decoded_token->username);
    									$query->execute();

    									$query->close();
    									$mysql->connection->close();

    									(object)$result->{key($JWT)} = $new_token;
    									return ['information' => $decoded_token, 'new_token' => $result];

    								} else { return false; }
    							}
    							else { return false; }
    						}
    						else { return false; }
    					}
    					catch (Exeption $e)
    					{
    						$this->logger->log(
                                $this->logName,
                                'Unable to decode JWT: '.$e->errno.' : '.$e
                            );
    						return false;
    					}
    				}
    				else
    				{
    					$this->logger->log(
                            $this->logName,
                            'Token missmatch error!'
                        );
    					return false;
    				}
                }
    			$this->logger->log(
                    $this->logName,
                    'Unable to read the token from the database: '.$connection->errno
                );
                $mysql->connection->close();
                return false;
            }

        }

		/*
		*
		* */
        public function delete ($JWT)
        {
			$mysql = parent::connect();
            if (!$mysql->error)
            {
                if ($query = $mysql->connection->prepare('DELETE * FROM ACTIVE_USERS WHERE ID = ? LIMIT 1'))
    			{
    				$query->bind_param('i', key($JWT));
    				$query->execute();

    				$query->close();
    				$mysql->connection->close();

    				$result = $this->readActive(key($JWT));
    				if (mysql_num_rows($result) == 0) { return true; }
    				else { return false; }
    			}
    			$mysql->connection->close();
    			return false;
            }
        }

		/*
		*
		* */
		private function readActive ($id)
		{
			if (!is_numeric($id)) {
				$this->logger->log(
                    $this->logName,
                    'Can\'t read active user by id with a NaN id.'
                );
				return false;
			}
			$id = intval($id);

			$mysql = parent::connect();

            if (!$mysql->error)
            {
                if ($query = $mysql->connection->prepare('SELECT * FROM ACTIVE_USERS WHERE ID = ?'))
    			{
    				$query->bind_param('i', $id);
    				$query->execute();

    				$query->bind_result($result);
    				$query->fetch();

    				$query->close();
    				$mysql->connection->close();
    				return $result;
    			}
    			$this->logger->log(
                    $this->logName,
                    'Unable to query active user by id: '.$mysql->connection->errno
                );
    			$mysql->connection->close();
    			return false;
            }
		}

		/*
		*
		* */
		private function confirmUser ($username, $password)
		{
			$mysql = parent::connect();

            if (!mysql->error)
            {
                if ($query = $mysql->connection->prepare('SELECT PASSWORD FROM USERS WHERE USERNAME = ?'))
    			{
    				$query->bind_param('s', $username);
    				$query->execute();

    				$query->bind_result($hash);
    				$query->fetch();

    				$query->close();
    				$mysql->connection->close();

    				if ($password === $hash) { return true; }
    				else { return false; }
    			}
    			$this->logger->log(
                    $this->logName,
                    'Could not confirm user information: '.$mysql->connection->errno
                );
    			$connection->close();
    			return false;
            }
		}

    }
?>
