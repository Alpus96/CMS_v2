<?php

    /**
     *  This class is a random string generator.
     *
     *   @category       Variables
     *   @package        helper
     *   @subpackage     radnom_variables
     *   @version        1.1.3
     *   @since          1.0.0
     *   @deprecated     ---
     */
    class RandStr {

        /**
         *  @method
         * 
         *  @param      integer       : The desired length of the generated string.
         *  @param      string        : A custom charset string to pick characters from.
         * 
         *  @return     string        : A random string.
         * 
         *  @throws     InvalidArgumentException
         *  @throws     OutOfRangeException
         */
        function __construct ($length = 16, $custom_charset = null) {
            //  Confirm the passed parameters are valid.
            if (!is_integer($length)) 
            { throw new InvalidArgumentException('Invalid length type, must be of type integer.'); }
            if ($length < 1 || $length > 536870912) 
            { throw new OutOfRangeException('Desired string length out of range. (min: 1, max: 536\´870\´912)'); }
            if (!is_null($custom_charset) && !is_string($custom_charset))
            { throw new InvalidArumentException('Invalid custom charset, must be of type string.'); }

            //  Set the charset to custom charset.
            $chars = $custom_charset;
            //  If charset is null apply default charset.
            if ($chars === null) {
                $nums = '01234567890123456789';
                for ($i = 0; $i < 6; $i++) { $nums.= rand(0, 9); }
                $sm_chars = 'abcdefghijklmnopqrstuvwxyz';
                $lg_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $chars = str_shuffle($nums.$sm_chars.$lg_chars);
            }
            
            $chars_len = strlen($chars);
            $rand_str = '';
            //  Randomly concat chars from charset until desired length.
            for ($i = 0; $i < $length; $i++) {
                $rand_str.= $chars[rand(0, $chars_len-1)];
            }
            //  Return the random string.
            return $rand_str;
        }

    }

?>