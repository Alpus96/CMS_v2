<?php

    //  TODO:   Comment.

    class InvalidImageException extends Exception {

        function __construct (Exception $previous = null) {
            parent::__construct('Invalid image type. (Accepts: .jpeg, .png, .gif)', 4, $previous);
        }

        function __toString() {
            return __CLASS__ . ": [{$this->code}]: {$this->message}\n".parent::getTraceAsString();
        }

    }
?>