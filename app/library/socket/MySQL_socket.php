<?php
    /**
     *  TODO:   Add comments.
     */
    class MySQL_Socket
    {

        static private $Logger;
        static private $logName;
        static private $socketConfig;

        protected $error;

        protected function __construct()
        {
            $this->Logger = new Logger();
            $this->logName = '_SQL_Socket_Errors';

            $JSON_Socket = new JSON_Socket();
            $this->socketConfig = $JSON_Socket->read('MySQL_Socket_Config');

            if ($this->socketConfig->error)
            {
                $this->error = $this->socketConfig->error;
                $this->Logger->log($this->logName, 'Error loading the MySQL configuration; '.$this->error);
            }
        }

        protected function connect()
        {
            $connection = mysqli_connect(
                $this->sqlCridentials[0],
                $this->sqlCridentials[1],
                $this->sqlCridentials[2],
                $this->sqlCridentials[3]
            );

            if (!$connection->connect_error && $connection->set_charset($this->sqlCharset))
            {
                return (object) [
                    'error_status' => false,
                    'error' => NULL,
                    'connection' => $connection
                ];
            }
            else
            {
                $this->error = $connection->error;
                $this->Logger->log($this->logName, 'Error connecting to the MySQL database; '.$this->error);

                return (object) [
                    'error_status' => true,
                    'error' => $this->error,
                    'connection' => NULL
                ];
            }
        }

    }
 ?>