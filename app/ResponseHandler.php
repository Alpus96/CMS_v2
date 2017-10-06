<?php

require_once 'app/library/debug/logger.php';
require_once 'app/library/socket/JSON_socket.php';

require_once 'app/objects/users/Users.php';
require_once 'app/objects/users/Admins.php';

//  contents
//  Xcontents

class ResponseHandler {
    static private $url;
    static private $baseURL;

    static private $logger;
    static private $logName;

    static private $JSON;
    static private $config;

    public function __construct($url) {
        self::$logger = new logger();
        self::$logName = '_urlHandler_log';

        if (!is_string($url)) {
            $msg = 'The passed URL was not a string.';
            self::$logger->log(self::$logName, $msg);
            throw new Exception($msg);
        }

        self::$url = $url;
        self::$JSON = new JSON_socket();
        self::$config = self::$JSON->read('responseConfig');

        self::$baseURL = 'http://localhost/projects/CMS_v2/';
    }

    public function get () {
        $token = $_COOKIE['token'] ? json_decode($_COOKIE['token'])->value : false;

        if (self::$url === '/') {
            echo file_get_contents(self::$config->index);
        } else if (self::$url === '/login') {
            echo file_get_contents(self::$config->login);
        } else if (self::$url === '/edit') {
            $approved = false;
            if ($token) {
                $user = new User($token);
                $new_token = $user->getToken();
                if ($new_token) {
                    $approved = true;
                    $index = file_get_contents(self::$config->index);
                    $index = str_replace("<!-- edit -->", self::$config->src_editor, $index);
                    $index = str_replace("<!-- edit_menu -->", self::$config->edit_menu, $index);
                    echo $index;
                }
            }
            if (!$approved) { header('Location: '.self::$baseURL.'login'); }
        } else if (self::$url === '/settings') {
            //  TODO:  Handle checking if logged in and if admin.
            $approved = false;
            if ($token) {
                $user = new User($token);
                if ($user->getToken()) {
                    $approved = true;
                    $settings = file_get_contents(self::$config->settings);
                    $admin = new Admin($token);
                    if ($admin->is_admin()) {
                        $settings = str_replace("<!--  admin_menu  -->", self::$config->admin_menu, $settings);
                        $settings = str_replace("<!--  admin_tabs  -->", self::$config->admin_tabs, $settings);
                    }
                    echo $settings;
                }
            }
            if (!$approved) { header('Location: '.self::$baseURL.'login'); }
        } else {
            echo file_get_contents(self::$config->fourOfour);
        }
    }

    public function post() {
        $data = json_decode(file_get_contents('php://input'));
        $token = $_COOKIE['token'] ? json_decode($_COOKIE['token'])->value : false;

        if (self::$url === '/login') {
            $credentials = (object)[
                'username' => base64_decode($data->username),
                'password' => base64_decode($data->password)
            ];
            $user = new User($credentials);
            $token = $user->getToken();
            $res = (object)[
                'success' => $token ? true : false,
                $token ? 'token' : 'error' => $token ? $token : 'Fel användarnamn eller lösenord!'
            ];
            echo json_encode($res);
        }
        else if (self::$url === '/logout') {
            $res = (object)['success' => false];
            if ($token) {
                $user = new User($token);
                $res->success = $user->logout();
            }
            echo json_encode($res);
        }
        else if (self::$url === '/setPW') {
            $newPass = base64_decode($data->password);
            $user = new User($token);
            echo json_encode((object)['success' => $user->newPassword($newPass)]);
        }
        else if (self::$url === '/getUsers') {

        }
        else if (self::$url === '/newUser') {

        }
        else if (self::$url === '/setUserPW') {

        }
        else if (self::$url === '/toggleUserLock') {

        }
        else if (self::$url === '/setUserType') {

        }
        else if (self::$url === '/deleteUser') {

        }
        else if (self::$url === '/DBCredentials') {

        }
        else if (self::$url === '/tokenKey') {

        }
        else { http_response_code(404); }
    }
}

?>