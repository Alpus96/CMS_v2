<?php

    //  TODO:   Comment.
    
    class JsonDecodeException extends Exception {

        function __construct (Exception $previous = null) { parent::__construct('Unable to decode bad json string.', 1, $previous); }

        function __toString() { return __CLASS__ . ": [{$this->code}]: {$this->message}\n".parent::getTraceAsString(); }

    }
?>