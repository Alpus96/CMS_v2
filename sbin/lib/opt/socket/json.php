<?php

    /**
     *  TODO:   Rewrite to use exceptions and file tool.
     */
    
    //  Require the file tool.
    require_once dirname(__DIR__).'/tool/file.php';
    //  Require the error tool.
    require_once dirname(__DIR__).'/tool/error.php';

    /**
     *   This class handels CRUD actions to .json files in the json library.
     *
     *  @uses           FileTool
     *  @uses           ErrorTool
     *
     *  @category       Datastoreage
     *  @package        dataSockets
     *  @subpackage     json
     *  @version        1.1.0
     *  @since          1.0.0
     *  @deprecated     ---
     */

    class JsonSocket extends FileTool {

        static private $err_tool;

        /**
        *   @method     Ensures that the json library folder exists.
        *
        *   @param      string        : The path to the desired library folder.
        * */
        function __construct ($lib_path) {
            parent::__construct($lib_path);
            self::$err_tool = new ErrorTool('JSON_SOCKET');
        }

        /**
         *  @method     Creates a new json file if it does not
         *              exist and writes passed data to it.
         *
         *  @param      string        : The name of the file to create.
         *  @param      mixed         : The data to write to the json file.
         *  @param      string        : The sub path in the json library.
         */
        function newJSON ($filename, $data = null, $dir = null) {
            //  Confirm the passed paramaters are of valid type.
            if (!is_string($filename))
            { throw new InvalidArgumentException('A filename must be a string.'); }
            if (!is_null($dir) && !is_string($dir))
            { throw new InvalidArgumentException('A sub directory path must be a string.'); }
            //  Secure trailing slash in dir path.
            if ($dir[strlen($dir)] != '/') { $dir.= '/'; }
            //  Create relative path for new json file.
            $rel_path = $dir !== null ? $dir.$filename.'.json' : $filename.'.json';
            //  Create the new json file and return true or false.
            return parent::create($rel_path, $data) ? true : false;
        }

        /**
        *   @method     Reads the contents of the json file.
        *
        *   @param      string  The name of the file to read.
        *
        *   @return     object  The parsed data from the json file.
        * */
        function getData ($filename, $dir = null) {
            //  Confirm the passed parameters are of valid type.
            if (!is_string($filename)) 
            { throw new InvalidArgumentException('A filename must be a string.'); }
            if (!is_null($dir) && !is_string($dir))
            { throw new InvalidArgumentException('A sub directory path must be a string.'); }
            //  Secure trailing slash in dir path.
            if ($dir[strlen($dir)] != '/') { $dir.= '/'; }
            //  Create relative path for new json file.
            $rel_path = $dir !== null ? $dir.$filename.'.json' : $filename.'.json';
            $data = parent::read($rel_path);
            return $data ? $data : false;
        }

        /**
        *   @method     Overwrites a json file with passed data.
        *
        *   @param      string  The name of the json file to write to.
        *   @param      object  The data to write to the json file.
        *
        *   @return     boolean Signaling wheather the file was updated.
        * */
        function updateData ($filename, $data = null, $dir = null) {
            if (!is_string($filename))
            { throw new InvalidArgumentException('A filename must be a string.'); }
            if (!is_null($dir) && !is_string($dir))
            { throw new InvalidArgumentException('A sub directory path must be a string.'); }
            //  Secure trailing slash in dir path.
            if (!is_null($dir) && $dir[strlen($dir)] != '/') { $dir.= '/'; }
            //  Create relative path for new json file.
            $rel_path = $dir !== null ? $dir.$filename.'.json' : $filename.'.json';
            return parent::update($rel_path, $data);
        }

        /**
        *   @method     Removes a json file from the library.
        *
        *   @param      string  The name of the file to delete.
        *
        *   @return     boolean Signaling wheather the file was removed.
        * */
        function delete ($file_name) {
            //  Concatinate the absolute path to the new json file.
            $file_path = self::$lib_path.$file_name.'.json';
            //  If the file exists remove it.
            if (file_exists($file_path)) {
                return unlink($file_path);
            }
            //  If the file did not exist return true,
            //  as it was the same result as removing it.
            return true;
        }

    }
?>
