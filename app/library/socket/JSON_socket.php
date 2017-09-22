<?php
    /**
     *  @description    This class handles CRUD functions for the JSON library.
    * */
    class JSON_socket
    {
        //  Logger inctance and log file name for debuging.
        static private $logger;
        static private $logName;

        private $libraryPath;    //  Path to JSON library directory.
        public $error;        //  Class error variable.

        /*
        *   @description    The constructor sets the dafult values of the class variables.
        *
        *   @arguments      $lib_path : the alternative path for the JSON library.
        * */
        public function __construct ($lib_path = false, $mkdir = false)
		{
            //  Inctance the logger and set the log file name.
            self::$logger = new logger();
            self::$logName = '_JSON_Socket_errorLog';

            //  Check if a library path was passed as an argument.
            if (is_string($lib_path))
            {
                //  Confirm that the path is an existing directory.
                if (file_exists($lib_path))
                {
                    //  Set the passed path as the defult path
                    //  if it was an existing directory.
                    $this->libraryPath = $lib_path;
                }
                else if ($mkdir === true)
                {
                    //  If the path was not an existing directory try to create it.
                    try
                    {
                        mkdir($lib_path);
                        //  When the directory has been created
                        //  set the passed path as the default path.
                        $this->libraryPath = $lib_path;
                    }
                    catch (Exeption $e)
                    {
                        //  If unable to create the directory
                        //  log the error before throwing it.
                        $msg = 'Unable to create the JSON library directory at '.$lib_path.'.';
                        self::$logger->log(
                            self::$logName,
                            $msg
                        );
                        throw new Exeption($msg);
                    }
                }
                else
                {
                    $msg = 'Unable to set JSON library directory, no such directory : '.$lib_path.'. Please enable mkdir on construct or create the directory.';
                    throw new Exeption($msg);
                }
            }
            else
            {
                //  If there was no path passed Set the default
                //  absolute path to the JSON library.
    			$this->libraryPath = "app/library/JSON/";
            }

            //  Set the default error status to false,
            $this->error = '';
        }

        /*
        *   @description    The create function creates a new json file
        *                   in the JSON directory, if the passed file name
        *                   is not already in use.
        *
        *   @arguments      $name       : The name of the file to create.
        *                   $content    : The initial data to put in the file.
        * */
        public function create ($name, $content)
        {
            $this->error = '';
            //  Set the absolute path to the file being created.
            $path = $this->libraryPath.$name.'.json';
            //  Confirm it does not already exist.
            if (!file_exists($path))
            {
                //  If the file did not exist, try creating it.
                try
                {
                    touch($path);
                    //  If the file was created successfully update
                    //  it to contain the given content and return the result.
                    return $this->update($name, $content);
                }
                catch(Exeption $e)
                {
                    //  If creating the file failed log
                    //  the error before returning false.
                    $msg = 'There was an error creating the new JSON file '.$name.' : '.$e;
                    self::$logger->log(
                        self::$logName,
                        $msg
                    );
                    $this->error = $msg;
                    return false;
                }
            }
            //  If the file did exist return false.
            $this->error = 'File already exists.';
            return false;
        }

        /*
        *   @description    This function returns the decoded JSON object
        *                   of the content in the file with the passed name
        *                   in the set directory.
        *
        *   @arguments      $name       : The name of the file to read.
        * */
        public function read ($name)
        {
            $this->error = '';
            //  Set the absolute path to the file to read.
            $path = $this->libraryPath.$name.'.json';

            //  Confirm the file exists.
            if (file_exists($path))
            {
                //  If the file exists read the content.
                $jsonfile = file_get_contents($path);
                //  Return the content JSON decoded.
                return json_decode($jsonfile);
            }
            //  If the file did not exist log the problem and return false.
            $this->error = 'Unable to read, could not find file '.$name.' in directory '.$this->libraryPath.'.';
            self::$logger->log(
                self::$logName,
                $this->error
            );
            return false;
        }

        /*
        *   @description    This function replaces the content of a file.
        *
        *   @arguments      $name       : The name of the file to update.
        *                   $data       : The data to JSON encode and put into the file.
        * */
        public function update ($name, $data)
        {
            //  Set the absolute path to the file to update.
            $path = $this->libraryPath.$name.'.json';

            //  Confirm that the file exists.
            if (file_exists($path))
            {
                //  If the file exists return the result of writing
                //  the JSON encoded data to it.
                return file_put_contents(
                    $path,
                    json_encode(
                        $data,
                        JSON_PRETTY_PRINT
                    )
                );
            }
            //  If the file did not exist log the problem before returning false.
            $msg = 'Unable to update, did not find file '.$name. ' in directory '.$this->libraryPath.'.';
            self::$logger->log(
                self::$logName,
                $msg
            );
            $this->error = $msg;
            return false;
        }

        /*
        *   @description    This function removes the specified file.
        *
        *   @arguments      $name       : The name of the file to remove.
        * */
        public function delete ($name)
        {
            //  Set the absolute path to the file to delete.
            $path = $this->libraryPath.$name.'.json';

            //  Confirm that the file exists.
            if (file_exists($path))
            {
                //  If the file did exist return the result of deleting it.
                return unlink($path);
            }
            //  If the file did not exist log the problem before returning false.
            $msg = 'Unable to delete, did not find file '.$name. ' in directory '.$this->libraryPath.'.';
            self::$logger->log(
                self::$logName,
                $msg
            );
            $this->error = $msg;
            return false;
        }

    }
?>
