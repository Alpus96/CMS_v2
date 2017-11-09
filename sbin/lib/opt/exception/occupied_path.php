<?php

    //  TODO:   Comment.

    class OccupiedPathException extends Exception {
        
        function __construct (Exception $previous = null) {
            parent::__construct('Specified path is occupied.', 2, $previous);
        }
        
        function __toString() {
            return __CLASS__ . ": [{$this->code}]: {$this->message}\n".parent::getTraceAsString();
        }

    }
?>