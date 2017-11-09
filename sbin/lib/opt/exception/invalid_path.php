<?php

    //  TODO:   Comment.

    class InvalidPathException extends Exception {

        function __construct (Exception $previous = null) {
            parent::__construct('Invalid path.', 3, $previous);
        }

        function __toString() {
            return __CLASS__ . ": [{$this->code}]: {$this->message}\n".parent::getTraceAsString();
        }

    }
?>