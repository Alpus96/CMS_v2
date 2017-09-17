<?php
    /*
    *   @description    This class handels the connection to the database.
    * */
    class MySQL_socket
    {
        //  Logger class instance varaible and log file name.
        static private $logger;
        static private $logName;

        //  MySQL cridentials and charset configuration.
        static private $socketConfig;
        
        protected $error;   //  Class error message.

        /*
        *   @description    The constructor of this class sets up an instance
        *                   of the logger class, JSON_socket class and confirms
        *                   that the MySQL configuration file was read with the
        *                   correct properties.
        *
        *   @throws         Could not read the configuration file.
        *                   Incorrect configuration set.
        * */
        protected function __construct()
        {
            //  Create the logger class instance and set the name of the logfile.
            $this->logger = new logger();
            $this->logName = '_MySQL_socket_errorLog';

            //  Instance the JSON socket to read the MySQL cridentials configuration.
            $JSON_socket = new JSON_socket();
            $this->socketConfig = $JSON_Socket->read('MySQL_Config');

            //  Confirm there was no error reading the configuration file.
            if ($JSON_socket->error)
            {
                //  If the file was not read log the error and throw it.
                $msg = 'Error reading the MySQL configuration : '.$JSON_socket->error;
                $this->logger->log(
                    $this->logName,
                    $msg
                );
                $this->error = $msg;
                throw new Exeption($msg);
            }
            else if (array_keys((array)$this->socketConfig) != ['cridentials','charset'] && array_keys((array)$this->socketConfig->cridentials) != ['host','user','pw','db'] )
            {
                //  If there was no error reading the file
                //  but it did not contain the required information,
                //  log the problem and throw it as an error.
                $msg = 'Error confirming the MySQL configuration. The configuration did not match the required set.';
                $this->logger->log(
                    $this->logName,
                    $msg
                );
                $this->error = $msg;
                throw new Exeption($msg);
            }
        }

        /*
        *   @description    This function tries to connect to the MySQL database using the
        *                   cridentials in the configuration file.
        *
        *   @returns        (object) {error: boolean, connection: MySQL_connection}
        * */
        protected function connect()
        {
            //  Connect to the database using the loaded cridentials.
            $connection = mysqli_connect(
                $this->socketConfig->cridentials->host,
                $this->socketConfig->cridentials->user,
                $this->socketConfig->cridentials->pw,
                $this->socketConfig->cridentials->db
            );

            //  Confirm there was no error connecting to the database.
            if (!$connection->connect_error && $connection->set_charset($this->socketConfig->charset))
            {
                //  If all is ok, return the object with a connection.
                return (object) [
                    'error' => false,
                    'connection' => $connection
                ];
            }
            else
            {
                //  If there was an error handel it.
                $msg = 'Error connecting to the MySQL database; '.$connection->error;
                $this->error = $msg;
                $this->logger->log(
                    $this->logName,
                    $msg
                );

                //  Then return the object with error set to true.
                return (object) [
                    'error' => true,
                    'connection' => $connection
                ];
            }
        }

    }
 ?>
