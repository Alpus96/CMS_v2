<?php

require_once 'app/library/debug/logger.php';
require_once 'app/library/socket/JSON_socket.php';

require_once 'app/objects/users/Users.php';
require_once 'app/objects/users/Admins.php';

require_once 'app/objects/contents/Contents.php';
require_once 'app/objects/contents/ContentsEditor.php';
//  ContentsIndex
//  ContentsDeleted

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
                        $settings = str_replace("<!--  admin_scripts  -->", self::$config->admin_scripts, $settings);
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

        if (self::$url === '/getContentsByMarker') {
            $res = (object)['success' => false];
            if (property_exists($data, 'marker')) {
                $contents = new Contents();
                $res->data = $contents->getByMarker($data);
                $res->success = $res->data ? true : false;
            }
            echo json_encode($res);
        }
        else if (self::$url === '/getContentsByID') {
            $res = (object)['success' => false];
            if (property_exists($data, 'id')) {
                $contents = new Contents();
                $res->data = $contents->getById($data->id);
                $res->success = $res->data ? true : false;
            }
            echo json_encode($res);
        }
        else if (self::$url === '/newContents') {
            $res = (object)['success' => false];
            if (property_exists($data, 'text') && property_exists($data, 'marker')) {
                $contentsEditor = new ContentsEditor($token);
                $contentsEditor->createContents($data);
            }
        }
        else if (self::$url === '/getMD') {
            $res = (object)['success' => false];
            if (property_exists($data, 'id')) {
                $contentsEditor = new ContentsEditor($token);
                $res->data = $contentsEditor->getAsMD($data->id);
                $res->success = $res->data ? true : false;
            }
            echo json_encode($res);
        }
        else if (self::$url === '/updateContents') {
            $res = (object)['success' => false];
            if (property_exists($data, 'id') && property_exists($data, 'newText')) {
                $contentsEditor = new ContentsEditor($token);
                $res->data = $contentsEditor->updateContents($data);
                $res->success = $res->data ? true : false;
            }
            echo json_encode($res);
        }
        else if (self::$url === '/deleteContents') {
            $res = (object)['success' => false];
            if (property_exists($data, 'id')) {
                $contentsEditor = new ContentsEditor($token);
                $contentsEditor->deleteContents($data);
            }
            echo json_encode($res);
        }
        else if (self::$url === '/login') {
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
            if ($token) {
                $user = new User($token);
                $user->getToken() ? $user->logout() : null;
            }
            $res = (object)['success' => true];
            echo json_encode($res);
        }
        else if (self::$url === '/setPW') {
            //  TODO:  Require to give current password as confirmation.
            $newPass = base64_decode($data->password);
            $user = new User($token);
            echo json_encode((object)['success' => $user->newPassword($newPass)]);
        }
        else if (self::$url === '/getUsers') {
            $admin = new Admin($token);
            $users = $admin->getAllUsers();
            $res = (object)[
                'success' => $users ? true : false,
                 'data' => $users
            ];
            echo json_encode($res);
        }
        else if (self::$url === '/newUser') {
            $res = (object)['success' => false];
            if (property_exists($data, 'username') && property_exists($data, 'password') && property_exists($data, 'type')) {
                $admin = new Admin($token);
                $res->success = $admin->createUser($data);
            }
            echo json_encode($res);
        }
        else if (self::$url === '/toggleUserLock') {
            $res = (object)['success' => false];
            if (property_exists($data, 'username')) {
                $admin = new Admin($token);
                $res->success = $admin->toggleLockedUser($data->username);
            }
            echo json_encode($res);
        }
        else if (self::$url === '/setUserType') {
            $res = (object)['success' => false];
            if (property_exists($data, 'username') && property_exists($data, 'type')) {
                $admin = new Admin($token);
                $res->success = $admin->changeUserType($data->username, $data->type);
            }
            echo json_encode($res);
        }
        else if (self::$url === '/deleteUser') {
            $res = (object)['success' => false];
            if (property_exists($data, 'username')) {
                $admin = new Admin($token);
                $res->success = $admin->deleteUser($data->username);
            }
            echo json_encode($res);
        }
        else { http_response_code(404); }
    }
}

?>