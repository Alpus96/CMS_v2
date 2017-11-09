<?php

    function error_handle ($err_no, $err_str, $err_file, $err_line) {

        //  If error was supressed with @ operator execute the internal error handler.
        if (0 === error_reporting()) { return false;}

        //  TODO:   Log the error.
        switch ($err_no) {
            case E_ERROR:               throw new ErrorException            ($err_msg, 0, $err_severity, $err_file, $err_line);
            case E_WARNING:             throw new WarningException          ($err_msg, 0, $err_severity, $err_file, $err_line);
            case E_PARSE:               throw new ParseException            ($err_msg, 0, $err_severity, $err_file, $err_line);
            case E_NOTICE:              throw new NoticeException           ($err_msg, 0, $err_severity, $err_file, $err_line);
            case E_CORE_ERROR:          throw new CoreErrorException        ($err_msg, 0, $err_severity, $err_file, $err_line);
            case E_CORE_WARNING:        throw new CoreWarningException      ($err_msg, 0, $err_severity, $err_file, $err_line);
            case E_COMPILE_ERROR:       throw new CompileErrorException     ($err_msg, 0, $err_severity, $err_file, $err_line);
            case E_COMPILE_WARNING:     throw new CoreWarningException      ($err_msg, 0, $err_severity, $err_file, $err_line);
            case E_USER_ERROR:          throw new UserErrorException        ($err_msg, 0, $err_severity, $err_file, $err_line);
            case E_USER_WARNING:        throw new UserWarningException      ($err_msg, 0, $err_severity, $err_file, $err_line);
            case E_USER_NOTICE:         throw new UserNoticeException       ($err_msg, 0, $err_severity, $err_file, $err_line);
            case E_STRICT:              throw new StrictException           ($err_msg, 0, $err_severity, $err_file, $err_line);
            case E_RECOVERABLE_ERROR:   throw new RecoverableErrorException ($err_msg, 0, $err_severity, $err_file, $err_line);
            case E_DEPRECATED:          throw new DeprecatedException       ($err_msg, 0, $err_severity, $err_file, $err_line);
            case E_USER_DEPRECATED:     throw new UserDeprecatedException   ($err_msg, 0, $err_severity, $err_file, $err_line);
        }

        //  Do not execute internal error handler.
        return true;
    }

    /**
     *  TODO:   Write these error classes.
     *  NOTE:   What about handling custom error classess thrown?
     */
    class WarningException              extends ErrorException {}
    class ParseException                extends ErrorException {}
    class NoticeException               extends ErrorException {}
    class CoreErrorException            extends ErrorException {}
    class CoreWarningException          extends ErrorException {}
    class CompileErrorException         extends ErrorException {}
    class CompileWarningException       extends ErrorException {}
    class UserErrorException            extends ErrorException {}
    class UserWarningException          extends ErrorException {}
    class UserNoticeException           extends ErrorException {}
    class StrictException               extends ErrorException {}
    class RecoverableErrorException     extends ErrorException {}
    class DeprecatedException           extends ErrorException {}
    class UserDeprecatedException       extends ErrorException {}

?>