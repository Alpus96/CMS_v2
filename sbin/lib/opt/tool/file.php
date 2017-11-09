<?php

    //  Require used custom exceptions.
    require_once dirname(__DIR__).'/exception/invalid_path.php';
    require_once dirname(__DIR__).'/exception/occupied_path.php';
    require_once dirname(__DIR__).'/exception/json_decode.php';
    //  Require error helper.
    require_once dirname(__DIR__).'/tool/error.php';
    
    /**
     *   This class is a helper to handle files on the server.
     * 
     *  @uses           InvalidPathException
     *  @uses           OccupiedPathException
     *  @uses           JsonDecodeException
     *  @uses           ErrorTool
	 *
	 *  @category       Datastorage
	 *  @package        Helper
	 *  @version        1.0.2
	 *  @since          1.0.0
	 *  @deprecated     ---
     */

    class FileTool {

        static private $root_path;
        static private $err_tool;

        /**
         *  @method     This method instances this class.
         * 
         *  @param      string        : The root path for all files to handle. 
         *                              (i.e. the project root)
         * 
         *  @throws     InvalidArgumentException
         *  @throws     InvalidPathException
         */
        protected function __construct ($root_path) {
            //  Confirm the root path is a string.
            if (!is_string($root_path)) { throw new InvalidArgumentException('Root path must be a string.'); }
            //  Confirm the root path is an existing path, but not to a file.
            if (!file_exists($root_path) || is_file($root_path))
            //  If not an existing path or is a file throw invalid path exception.
            { throw new InvalidPathException(); }
            //  Put / at end of string if it is not there.
            if (substr($root_path, count($root_path)) !== '/') { $root_path.= '/'; }
            //  Save the root path in the class variable.
            self::$root_path = $root_path;
            //  Instance the error helper class.
            self::$err_tool = new ErrorTool('FILE_TOOL');
        }

        /**
         *  @method     Thsi function creates a file, and writes data to it if passed.
         * 
         *  @param      string        : The relative path to the file from root.
         *  @param      string        : The name of the file.
         *  @param      mixed         : The data to write to the new file, if any.
         * 
         *  @return     boolean|string: Representation of whether or not the operation 
         *                              succeded or the data that was writen to the file.
         * 
         *  @throws     OccupiedPathException
         *  @throws     
         */
        protected function create ($rel_path, $filename, $data = null) {
            //  Confirm the passed path and filename are strings.
            if (!is_string($rel_path) || !is_string($filename)) { return false; }
            //  Concat the strings to an absolute file path.
            $abs_path = self::$root_path.$rel_path.$filename;
            //  Confirm the file path is not in use.
            if (is_file($abs_path)) { throw new OccupiedPathException(); }
            //  Confirm sub dir(s) from root exist.
            $sub_dirs = explode('/', $rel_path);
            $sub_path = '';
            foreach ($sub_dirs as $sub_dir) {
                $sub_dir_abs_path = self::$root_path.$sub_path.$sub_dir;
                //  Create the directory if it does not exist.
                if (!file_exists($sub_dir_abs_path))
                { mkdir($sub_dir_abs_path); }
                $sub_path.= $sub_dir.'/';
            }
            //  Create the file.
            $file_handle = fopen($abs_path, 'w');
            //  Confirm the file stream is now open.
            if (!$file_handle) { return false; }
            //  If any data was passed as non-string json_encode it.
            if ($data != null && !is_string($data)) {
                try { $data = json_encode($data); }
                //  If the encode failed log the Exception.
                catch (Exception $e) {
                    self::$err_tool->log($e);
                    $data = false;
                }
            }
            //  If there is data write it to the file.
            if ($data) { fwrite($file_handle, $data); }
            //  Close the filestream.
            fclose($file_handle);
            //  Return true if there was no data. otherwise 
            //  return the data as write confirmation.
            return $data ? $data : true;
        }

        /**
         *  @method     Reads the data from the file and returns it.
         * 
         *  @param      string        : The relative path to the file from root.
         * 
         *  @return     mixed         : The string that was writen to the file or the json_decoded data.
         * 
         *  @throws     Exception     : InvalidPathException
         *  @throws     Exception     : JsonDecodeException
         */
        protected function read ($rel_path) {
            //  Confirm the passed relative path is a string.
            if (!is_string($rel_path)) { return false; }
            //  Concat an absolute path.
            $abs_path = self::$root_path.$rel_path;
            //  Confirm the path is a file.
            if (!is_file($abs_path)) { throw new InvalidPathException(); }
            //  If the path is valid open a file stream.
            $file_handle = fopen($abs_path, 'r');
            //  Read the contents from the file.
            $content = fread($file_handle, filesize($abs_path));
            //  Decode the contents if is resemples a json string.
            if (preg_match('/^{.+}$/', $content)) {
                try { $content = json_decode($content); }
                //  Throw JsonDecodeException if decode failed.
                catch (Exception $e) { throw new JsonDecodeException($e); }
            }
            //  Return the contents.
            return $content;
        }

        /**
         *  @method     This function overwrites the previous file contents with new data.
         * 
         *  @param      string        : The relative path to the file from root.
         *  @param      mixed         : The data to put as file contents.
         *  
         *  @return     boolean       : Representation of whether or not the operation succeded.
         * 
         *  @throws     Exception     : InvalidPathException
         */
        protected function update ($rel_path, $data) {
            //  Confirm the relative path is a string.
            if (!is_string($rel_path)) { return false; }
            //  Concat the strings to an absolute path.
            $abs_path = self::$root_path.$rel_path;
            //  Confirm the absolute path string is a file path.
            if (!is_file($abs_path)) { throw new InvalidPathException(); }
            //  Json encode the passed data if it is not already a string.
            if (!is_string($data)) {
                try { $data = json_encode($data); }
                //  Log an error and return false if unable to json_encode.
                catch (Exception $e) {
                    self::$err_tool->log($e);
                    return false;
                }
            }
            //  Put the data as contents in the file.
            return file_put_contents($abs_path, $data);
        }

        /**
         *  @method     This function appends data to a file, converts to array if json contents.
         * 
         *  @param      string        : The relative path from root to the file.
         *  @param      mixed         : The data to append to the file.
         * 
         *  @return     boolean       : Representation of whether or not the operation succeded.
         * 
         *  @throws     Exception     : InvalidPathException
         */
        protected function append ($rel_path, $data) {
            //  Confirm the relative path is a string.
            if (!is_string($rel_path)) { return false; }
            //  Concat the strings to an absolute path.
            $abs_path = self::$root_path.$rel_path;
            //  Confirm the absolute path is a file path.
            if (!is_file($abs_path)) { throw new InvalidPathException(); }
            //  Read the contents from the file.
            $content = self::read($rel_path);
            //  Evaluate the writeout data.
            $wo_data = null;
            //  If previous data and passed data are both strings concatinate them.
            if (is_string($content) && is_string($data)) { $wo_data.= $content.$data; }
            //  If previous data or passed data is not a string merge as array or extend array.
            else {
                if (is_array($content)) 
                { $content[count($content)] = $data; }
                else { $content = [$content, $data]; }
                //  Try encoding the new array as a json string.
                try { $wo_data = json_encode($content); }
                catch (Exception $e) {
                    self::$err_tool->log($e);
                    return false;
                }
            }
            //  Open a file stream.
            $file_handle = fopen($abs_path, 'w+');
            //  Write the data to the file.
            fwrite($file_handle, $wo_data);
            $success = false;
            //  Confirm the file contains the new data.
            if (fread($file_handle) === $wo_data) { $success = true; }
            //  Close the file stream.
            fclose($file_handle);
            //  Return whether or not the file contains the new data.
            return $success;
        }

        /**
         *  @method     This method moves a file to a new destination.
         * 
         *  @param      string        : The relative path from root.
         *  @param      string        : The path to where the file should be relative to root.
         * 
         *  @return     boolean       : Representation of whether the operation succeded.
         * 
         *  @throws     Exception     : InvalidPathException
         *  @throws     Exception     : OccupiedPathException
         */
        protected function move ($rel_path, $new_rel_path) {
            //  Confirm the  passed paths are strings.
            if (!is_string($rel_path) || !is_string($new_rel_path)) { return false; }
            //  Concat the current absolute path.
            $abs_path = self::$root_path.$rel_path;
            //  Confirm the file exists.
            if (!is_file($abs_path)) { throw new InvalidPathException(); }
            //  Concat the new absolute path.
            $new_abs_path = self::$root_path.$new_rel_path;
            //  Confirm the path is not in use.
            if (is_file($new_abs_path)) { throw new OccupiedPathException(); }
            //  Confirm the destination directory exists.
            if (!file_exists(dirname($new_abs_path))) { 
                //  If not try creating it.
                $sub_dirs = explode('/', dirname($new_rel_path));
                $sub_path = '';
                //  Loop and create directories until destination is reached.
                foreach ($sub_dirs as $sub_dir) {
                    $sub_dir_abs_path = self::$root_path.$sub_path.$sub_dir;
                    if (!file_exists($sub_dir_abs_path))
                    { mkdir($sub_dir_abs_path); }
                    $sub_path.= $sub_dir.'/';
                }
            }
            //  Rename / change the file location.
            return rename($abs_path, $new_abs_path);
        }

        /**
         *  @method     This function duplicates a file to a new path and/or filename.
         * 
         *  @param      string        : The relative path to the file.
         *  @param      string        : The new relative path to the file.
         * 
         *  @return     boolean       : Representation of whether the operation succeded.
         * 
         *  @throws     Exception     : InvalidPathException
         *  @throws     Exception     : OccupiedPathException
         */
        protected function copy ($rel_path, $cp_rel_path) {
            //  Confirm the  passed paths are strings.
            if (!is_string($rel_path) || !is_string($cp_rel_path)) { return false; }
            //  Concat the current absolute path.
            $abs_path = self::$root_path.$rel_path;
            //  Confirm the file exists.
            if (!is_file($abs_path)) { throw new InvalidPathException(); }
            //  Concat the new absolute path.
            $cp_abs_path = self::$root_path.$cp_rel_path;
            //  Confirm the path is not in use.
            if (is_file($cp_abs_path)) { throw new OccupiedPathException(); }
            //  Confirm the destination directory exists.
            if (!file_exists(dirname($cp_abs_path))) { 
                //  If not try creating it.
                $sub_dirs = explode('/', dirname($cp_rel_path));
                $sub_path = '';
                //  Loop and create directories until destination is reached.
                foreach ($sub_dirs as $sub_dir) {
                    $sub_dir_abs_path = self::$root_path.$sub_path.$sub_dir;
                    if (!file_exists($sub_dir_abs_path))
                    { mkdir($sub_dir_abs_path); }
                    $sub_path.= $sub_dir.'/';
                }
            }
            //  Copy the file to the new location.
            return copy($abs_path, $cp_abs_path);
        }

        /**
         *  @method     This function removes the file at the specified path.
         * 
         *  @param      string        : The relative path of the file to delete.
         * 
         *  @return     boolean       : Representation of whether the operation succeded.
         * 
         *  @throws     Exception     : InvalidPathException
         */
        protected function delete ($rel_path) {
            //  Confirm the passed relative path is a string.
            if (!is_string($rel_path)) { return false; }
            //  Concat the absolute path to the file.
            $abs_path = self::$root_path.$rel_path;
            //  Confirm the directory exists.
            if (!file_exists(dirname($abs_path)))
            //  If not the path is probably invalid.
            { throw new InvalidPathException(); }
            //  If the file does not exist is is deleted.
            if (!is_file($abs_path)) { return true; }
            //  If the file did exist delete it 
            //  now and return result of action.
            return unlink($abs_path);
        }

    }

    /**
     *  TODO: subDirsCheck &| Create function.
     */

?>