<?php
	//  This class handels all logging
	class Logger{

		/*
		*	Log variables:
		**/

		//	Log start string
		static private $logS;
		//	Log end string
		static private $logE;
		//
		static private $log_mode;

		/*
		*	Static: Log date (The log created and/or writen to
		*	will allways begin with the date the log was made.)
		**/
		static private $file_date;

		/*
		*	Static: Log time (The log writen to the log file
		*	will allways begin with the time the log was made.)
		**/
		static private $log_time;

		/*
		*	Static: Log filetype (The log will always be of the
		*	filetype .txt as specified in __construct().)
		**/
		static private $log_filetype;


		//  Setting log variables
		function __construct(){
			//	Private variables:
			$this->logS = "\t";
			$this->logE = "\n";
			$this->log_mode = 'a';

			//	Private Static variables:
			$this->log_filetype = '.txt';
			//	Set timezone to use when setting time variables:
			date_default_timezone_set('Europe/Stockholm');
			//	Static time variables
			$this->file_date = date('Y-m-d');
			$this->log_time = date('H:i:s');
		}


		/*
		*	This function writes $str to desired $log(opens or
		*	creates log file for the date the log occures), with
		*	a timestamp on the log entry.
		**/
		function log($logFile, $str){
			$file = fopen($this->file_date.$logFile.$this->log_filetype, $this->log_mode);
			fwrite($file, $this->logE.$this->log_time.$this->logS.$str);
			fclose($file);
		}

		/*
		*	Appends the last row of the log file with $str.
		**/
		function append_log($log, $str){
			$file = fopen($this->file_date.$log.$this->log_filetype, $this->log_mode);
			fwrite($file, $str);
			fclose($file);
		}

		/*
		*	This function is used if uniqe log is desired.
		**/
		function log_special($log, $str, $logS, $logE, $log_mode){
			$this->logS = $logS;
			$this->logE = $logE;
			$this->log_mode = $log_mode;

			$this->log($log, $str);
		}
	}
?>