<?php
require_once ROOT_PATH.'library/debug/Logger.php';
require_once ROOT_PATH.'library/socket/JsonSocket.php';

require_once ROOT_PATH.'app/controllers/user/UserHandler.php';
//require_once ROOT_PATH.'app/controllers/user/AdminHandler.php';

// require_once 'app/objects/contents/Contents.php';
// require_once 'app/objects/contents/ContentsEditor.php';
// require_once 'app/objects/contents/RemovedContents.php';
//  ContentsIndexing

class ResponseHandler {
    static private $url;
    static private $baseURL;

    static private $logger;
    static private $logName;

    static private $JSON;
    static private $config;

    public function __construct($url) {
        self::$logger = new Logger('responseHandler_log');

        //  TODO:   Handle url not valid type better.
        if (!is_string($url)) {
            $msg = 'The passed URL was not a string.';
            self::$logger->log($msg);
            //throw new Exception($msg);
        }

        self::$url = $url;
        self::$JSON = new JsonSocket(ROOT_PATH.'library/json_lib/');
        self::$config = self::$JSON->read('responseConfig');

        self::$baseURL = 'http://localhost/projects/CMS_v2/';
    }

    public function get () {
        $token = $_COOKIE['token'] ? json_decode($_COOKIE['token'])->value : false;

        if (self::$url === '/') {
            $index = file_get_contents(self::$config->index);
            $index = str_replace("<!-- pt -->", self::$config->indexTitle, $index);
            echo $index;
        }
        else if (self::$url === '/login') {
            $valid = false;
            if ($token) {
                $user = new userHandler();
                $valid = $user->verifyToken($token);
            }
            if ($valid) {
                header('Location: '.self::$baseURL.'edit');
            } else {
                $_COOKIE['token'] = '';
                echo file_get_contents(self::$config->login);
            }
        }
        else if (self::$url === '/edit') {
            $approved = false;
            if ($token) {
                $user = new userHandler();
                $valid = $user->verifyToken($token);
                if ($valid) {
                    $approved = true;
                    $index = file_get_contents(self::$config->index);
                    $index = str_replace("<!-- pt -->", 'CMS - Edit', $index);
                    $index = str_replace("<!-- edit -->", self::$config->src_editor, $index);
                    $index = str_replace("<!-- edit_menu -->", self::$config->edit_menu, $index);
                    echo $index;
                }
            }
            if (!$approved) { header('Location: '.self::$baseURL.'login'); }
        }
        else if (self::$url === '/settings') {
            $approved = false;
            if ($token) {
                $user = new userHandler();
                $valid = $user->verifyToken($token);
                if ($valid) {
                    $approved = true;
                    $settings = file_get_contents(self::$config->settings);
                    /*$admin = new Admin($token);
                    if ($admin->is_admin()) {
                        $settings = str_replace("<!--  admin_scripts  -->", self::$config->admin_scripts, $settings);
                        $settings = str_replace("<!--  admin_menu  -->", self::$config->admin_menu, $settings);
                        $settings = str_replace("<!--  admin_tabs  -->", self::$config->admin_tabs, $settings);
                    }*/
                    echo $settings;
                }
            }
            if (!$approved) { header('Location: '.self::$baseURL.'login'); }
        }
        else {
            echo file_get_contents(self::$config->fourOfour);
        }
    }

    public function post() {
        $data = json_decode(file_get_contents('php://input'));
        $token = $_COOKIE['token'] ? json_decode($_COOKIE['token'])->value : false;

        /*if (self::$url === '/getContentsByMarker') {
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
                $res->success = $contentsEditor->createContents($data);
            }
            echo json_encode($res);
        }
        else if (self::$url === '/getMD') {
            $res = (object)['success' => false];
            if (property_exists($data, 'id')) {
                $contentsEditor = new ContentsEditor($token);
                $res->data = $contentsEditor->getAsMD($data->id);
                $res->success = $res->data === false ? false : true;
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
                $res->success = $contentsEditor->deleteContents($data->id);
            }
            echo json_encode($res);
        }
        else if (self::$url === '/getDeleted') {
            $res = (object)['success' => false];
            $removedContents = new RemovedContents($token);
            $res->data = $removedContents->getRemovedContents();
            $res->success = $res->data ? true : false;
            echo json_encode($res);
        }
        else if (self::$url === '/restoreDeleted') {
            $res = (object)['success' => false];
            if (property_exists($data, 'id')) {
                $removedContents = new RemovedContents($token);
                $res->success = $removedContents->restoreContents($data->id);
            }
            echo json_encode($res);
        }
        else*/
        if (self::$url === '/login') {
            $cred = (object)[
                'username' => base64_decode($data->username),
                'password' => base64_decode($data->password)
            ];
            $user = new userHandler();
            $token = $user->login($cred->username, $cred->password);
            $res = (object)[
                'success' => $token ? true : false,
                'token' => $token ? $token : false
            ];
            echo json_encode($res);
        }
        else if (self::$url === '/logout') {
            //  TODO:  Handle errors.
            $res = (object)['success' => false];
            if ($token) {
                $user = new userHandler();
                $res->success = $user->logout($token);
            }
            if ($res->success) { $_COOKIE['token'] = ''; }
            echo json_encode($res);
        }
        else if (self::$url === '/setPW') {
            $res = (object)['success' => false];
            if (property_exists($data, 'password') && property_exists($data, 'newPass')) {
                $user = new User($token);
                if ($user->getToken()) {
                    $res->data = $user->newPassword(base64_decode($data->password), base64_decode($data->newPass));
                    $res->success = $res->data ? true : false;
                }
            }
            echo json_encode($res);
        }
        else if (self::$url === '/getAuthName') {
            $res = (object)['success' => false];
            if ($token) {
                $user = new userHandler();
                $res->data = $user->getDisplayName($token);
                $res->success = $res->data ? true : false;
            }
            echo json_encode($res);
        }
        else if (self::$url === '/setAuthName') {
            $res = (object)['success' => false];
            if ($token) {
                $user = new userHandler();
                $res->data = $user->setDisplayName($token);
            }
            if (property_exists($data, 'password') && property_exists($data, 'authName')) {
                $user = new User($token);
                if ($user->getToken()) {
                    $res->data = $user->setAuthorName(base64_decode($data->password), $data->authName);
                    $res->success = $res->data ? true : false;
                }
            }
            echo json_encode($res);
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