<?php

    require_once ROOT_PATH.'sbin/lib/proc/base/response_helper.php';
    require_once ROOT_PATH.'sbin/lib/opt/socket/json_socket.php';
    require_once ROOT_PATH.'sbin/app/proc/users/user.php';
    require_once ROOT_PATH.'sbin/app/proc/users/admin.php';
    // require_once ROOT_PATH.'sbin/app/proc/contents/entries.php';
    // require_once ROOT_PATH.'sbin/app/proc/contents/archive.php';
    // require_once ROOT_PATH.'sbin/lib/opt/socket/image_socket.php';

    /**
     *  This class sends a response for request to the server.
     *  
     *  TODO: Review uses
     * 
     *  @uses           ResponseHelper
     *  @uses           JsonSocket
     *  @uses           User
     *  @uses           Admin
     *  @uses           Entries
     *  @uses           Archive
     *  @uses           ImageSocket
     *
     *  @category       Request handling
     *  @package        Server index
     *  @subpackage     Request response
     *  @version        2.0.0
     *  @since          1.0.0
     *  @deprecated     -
     */

    class Response extends ResponseHelper {

        static private $logger;
        static private $json;
        static private $view_path;
        static private $token;

        /**
        *   @method     Handles the given url and method with a response
        * 
        *   @param      string      The requested url string.
        *   @param      string      The request method.
        * 
        *   @return     boolean     Returns false if the request url or method was of wrong type.
        * */
        function __construct ($url, $method) {
            parent::__construct();
            if (!is_string($url) || !is_string($method)) { return false; }
            if ($method != 'GET' && $method != 'POST') { return false; }

            self::$json = new JsonSocket(ROOT_PATH.'sbin/lib/etc/');
            self::$view_path = self::$json->read('view_paths');
            self::$token = $_COOKIE['token'] ? json_decode($_COOKIE['token'])->value : false;

            if ($method == 'GET') { self::GET($url); }
            else if ($method == 'POST') { self::POST($url); }
        }

        /**
         * 
         */
        static private function GET ($url) {
            if ($url === '/') {
                //  TODO:   Add page title.
                $index = file_get_contents(ROOT_PATH.self::$view_path->index);
                $editable = false;
                if (parent::isUser(self::$token)) 
                { $editable = parent::editable($index); }
                else { $index = str_replace('<!-- pt -->', '', $index); }
                echo $editable ? $editable : $index;
            } else if ($url === '/login') {
                echo file_get_contents(ROOT_PATH.self::$view_path->login);
            } else if ($url === '/cms') {
                if (parent::isUser(self::$token)) {
                    $cms = file_get_contents(ROOT_PATH.self::$view_path->cms);
                    if (parent::isAdmin(self::$token)) 
                    { $cms_a = parent::addAdminSettings($cms); }
                    echo $cms_a ? $cms_a : $cms;
                } else { echo file_get_contents(ROOT_PATH.self::$view_path->unauthorized); }
            } else if ($url === '/contentsByMarker') {

            } else if ($url === '/contentsById') {
                
            } else if ($url === '/contentAsMarkdown') {
                
            } else if ($url === '/archivedContents') {
                
            } else if ($url === '/authorName') {
                
            } else if ($url === '/allUsers') {

            } else {
                echo file_get_contents(ROOT_PATH.self::$view_path->fourOfour);
            }
        }

        static private function POST ($url) {
            //  Use post backend function response on data.
            $data = json_decode(file_get_contents('php://input'));

            //  restoreEntry
            //  toggleUserLock
            //  updatePassword
            //  updateAuthorname
            //  newUser
            //  updateUserType
            //  newEntry
            //  updateEntry
            //  newImage
            //  logout
            //  Remove entry
            //  Remove archived
            //  remove user
            //  remove image
            //

            if ($url === '/login') {

            } else if ($url === '/archiveEntry') {

            } else { echo json_encode((object)['success' => false, 'status' => 404]); }
        }

    }

?>