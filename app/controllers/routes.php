<?php
    /*
    *   TODO:   Require all files in the project. (library, models, views,
    *           controllers in that order)
    *
    *   TODO:   Complete routes to define the structure and implementation of
    *           the project.
    *
    *   NOTE:   Confirm token here for paths where it is nessesary.
    *   TODO:   Move functions to login, confirm active and logout user to user controller.
    *
    *   TODO:   Add paths for multiple item loading.
    * */

    require_once 'app/library/socket/JSON_socket.php';
    require_once 'app/library/socket/MySQL_socket.php';

    require_once 'app/library/plugin/jwt/JWT.php';
    require_once 'app/models/user/token_model.php';
    require_once 'app/library/socket/activeUser_socket.php';
    require_once 'app/models/user/user_model.php';
    require_once 'app/controllers/users.php';

    class Routes
    {
        private static $logger;
        private static $logName;

        private static $index;
        private static $login;
        private static $settings;
        private static $fourOfour;

        private static $src_editor;
        private static $edit_menu;

        function __construct () {
            require_once 'app/library/debug/logger.php';
            self::$logger = new logger();
            self::$logName = '_routesLog';

            require_once 'app/library/socket/JSON_socket.php';
            $JSON = new JSON_socket();
            $config = $JSON->read('routesConfig');

            self::$index = $config->index;
            self::$login = $config->login;
            self::$settings = $config->settings;
            self::$fourOfour = $config->fourOfour;

            self::$src_editor = $config->src_editor;
            self::$edit_menu = $config->edit_menu;
        }

        function get ($url)
        {
            //  NOTE:   The CMS be a subdomain to the "pages"?

			//	Load login page
            if (substr($url, 0, 1) === '/' && strlen($url) === 1) {
                //header('Content-Type: text/html');
                echo file_get_contents(self::$index);
            }
            //  Load the login page.
            else if (substr($url, 0, 6) === '/login' && strlen($url) === 6)
            { echo file_get_contents(self::$login); }
            //  Load the editable version of the index page.
            else if (substr($url, 0 , 5) === '/edit' && strlen($url) === 5)
            {
                $cookie = json_decode($_COOKIE['token']);
                $JWT = json_decode($cookie->value);

                $user = new User();
                if ($user->is_active($JWT)) {
                    $index = file_get_contents(self::$index);
                    $index = str_replace("<!-- edit -->", self::$src_editor, $index);
                    $index = str_replace("<!-- edit_menu -->", self::$edit_menu, $index);
                    echo $index;
                }
            }
            //  Load the manage page.
            else if (substr($url, 0, 9) === '/settings' && strlen($url) === 9)
            {
                $cookie = json_decode($_COOKIE['token']);
                $JWT = json_decode($cookie->value);

                $user = new User();
                if ($user->is_active($JWT)) { echo file_get_contents(self::$settings); } }
            //	Load post(s)
            else if (substr($url, 0, 5) === '/post')
            {
                // tag or specific
                if ($_GET['category'] && $_GET['amount'])
                {
                    $entry = (object)[
                        'id' => 324,
                        'content' => '<p>content text etc.</p>',
                        'timestamp' => time(),
                        'edited' => false
                    ];

                    $test = (object) [
                        '1st' => $entry
                    ];

                    echo json_encode(['success' => true, 'data' => json_encode($test)]);
                }
                else if ($_GET['id'])
                {
                    if ($_GET['asMD']){
                        $entry = (object)[
                            'id' => 324,
                            'content' => 'content text etc.',
                            'timestamp' => time(),
                            'edited' => true
                        ];

                        echo json_encode(['success' => true, 'data' => json_encode($entry)]);
                    } else {
                        echo json_encode(['success' => false]);
                    }
                }
                else
                {
                    http_response_code(404);
                }
            }
            //	Load article(s)
            else if (substr($url, 0, 8) === '/article')
            {
                // tag or specific
            }
			//	Load image post(s)
            else if (substr($url, 0, 10) === '/imagepost')
            {
                // tag or specific
            }
			//	Load Image(s)
            else if (substr($url, 0, 6) === '/image')
            {

            }
            else
            {
                //  If the route was not defined above,
                //  show 404 page with 5 sec redirect delay to '/'.
                echo file_get_contents(self::$fourOfour);
            }
        }

        function post ($url)
        {
            $data = json_decode(file_get_contents('php://input'));

            if (substr($url, 0, 6) === '/login')
            {
                $decoded_login = (object)[
                    'username' => base64_decode($data->username),
                    'password' => base64_decode($data->password)
                ];

                $user = new User($decoded_login->username);
                $is_loggedin = $user->authenticate($decoded_login->password);

                $res = (object)['success' => false];

                if ($is_loggedin) {
                    $res->success = true;
                    $res->token = $is_loggedin->toJSON();
                } else {
                    $res->error = $user->error;
                }

                header("Content-Type: application/json");
                echo json_encode($res);
            }
            else if (substr($url, 0, 7) === '/logout')
            {
                $cookie = json_decode($_COOKIE['token']);
                $JWT = json_decode($cookie->value);
                
                $user = new User();
                $res = (object)['success' => $user->logout($JWT)];

                header("Content-Type: application/json");
                echo json_encode($res);
            }
            else { http_response_code(404); }

			//	New post
			//	Update post
			//	Delete post

			//	New article
			//	Update article
			//	Delete article

			//	New image post
			//	Update image post
			//	Delete image post

			//	New image
			//	Update image
			//	Delete image

            //echo 'NOTE: POST has not yet been implemented.<br>';
        }

        private function req ($files = false)
        {
            return $files;
        }

    }
?>
