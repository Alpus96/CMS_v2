<?php
    /**
    *   This class handels writing error stacktraces 
    *   to a text file, to ease debuging.
	*
	*   @category       Debuging
	*   @package        Error stacktraces
	*   @version        1.0.0
	*   @since          1.0.0
	*   @deprecated     ---
    * */
        
    //  Require the log socket to use when writing the log files.
    require_once dirname(__DIR__).'/socket/log.php';

    //  Extend the log socket to ease use of ... functions.
    class ErrorTool extends LogSocket{

        /**
         *  @method     Initiates the error helper class by instacing the parent 
         *              log socket with id as part of log file name.
         * 
         *  @param      string        : The string name of the error helper.
         */
        function __construct ($helper_name) {
            //  Instance the parent with the helper id as part of log file name.
            parent::__construct($helper_name.'_ERROR_LOG');
        }

        /**
         *  @method     This function throws an exception with passed message and 
         *              logs the stacktrace in the log file.
         * 
         *  @param      string        : The message to log as an Exception stacktrace.
         */
        function stacktrace ($msg) {
            //  Throw the passed message as an Exception.
            try { throw new Exception($msg); }
            //  Catch the Exception and write it to the log file.
            catch (Exception $e) { parent::log($e); }
        }

    }

?>