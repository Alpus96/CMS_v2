<?php
    /*
    *   TODO:   Require all files in the project. (library, models, views,
    *           controllers in that order)
    *
    *   TODO:   Complete routes to define the structure and implementation of
    *           the porject.
    *
    *   NOTE:   Confirm token here for paths where it is nessesary.
    *
    *   TODO:   Add paths for miltiple item loading.
    * */
    class Routes
    {
        private static $index;
        private static $login;
        private static $manage;
        private static $src_editor;

        private static $logger;
        private static $logName;

        function __construct () {
            self::$index = dirname(dirname(__FILE__)).'/views/index.html';
            self::$login = dirname(dirname(__FILE__)).'/views/login.html';
            self::$manage = dirname(dirname(__FILE__)).'/views/manage.html';
            self::$src_editor = "<script src=\"/projects/CMS_v2/app/views/assets/lib/cookies.js\"></script>\n<script src=\"/projects/CMS_v2/app/views/assets/js/CMS_logout.js\"></script>\n<script src=\"/projects/CMS_v2/app/views/assets/js/CMSEditor.js\"></script>";

            require_once 'app/library/debug/logger.php';
            self::$logger = new logger();
            self::$logName = '_routesLog';
        }

        function get ($url)
        {
            //  NOTE:   The CMS be a subdomain to the "pages"?

			//	Load login page
            if (substr($url, 0, 1) === '/' && strlen($url) === 1) {
                //header('Content-Type: text/html');
                echo file_get_contents(self::$index);
            }
            //	Load post(s)
            else if (substr($url, 0, 5) === '/post')
            {
                // tag or specific
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
            //  Load the login page.
            else if (substr($url, 0, 6) === '/login' && strlen($url) === 6)
            {
                //
                header('Content-Type: text/html');
                echo file_get_contents(self::$login);
                //echo password_hash('pw', PASSWORD_DEFAULT);
            }
            //  Load the editable version of the index page.
            else if (substr($url, 0 , 5) === '/edit' && strlen($url) === 5)
            {
                require_once 'app/models/user/token_model.php';
                require_once 'app/library/socket/JSON_socket.php';
                require_once 'app/library/socket/MySQL_socket.php';
                require_once 'app/library/plugin/jwt/JWT.php';
                require_once 'app/library/socket/activeUser_socket.php';

                $cookie = json_decode($_COOKIE['token']);
                $JWT = json_decode($cookie->value);

                $token;
                try { $token = new Token($JWT->id, $JWT->token, $JWT->timestamp); }
                catch (Exception $e) { self::$logger->log(self::$logName, $e); }

                $is_active;
                if ($token)
                {
                    $active_users = new activeUser_socket();
                    $is_active = $active_users->confirm($token);

                    if ($is_active)
                    {
                        $index = file_get_contents(self::$index);
                        $index = str_replace(
                            "<!-- edit -->",
                            self::$src_editor,
                            $index
                        );
                        //header('Content-Type: text/html');
                        echo $index;
                    }
                }
                if (!$token || !$is_active) { header('location: /projects/CMS_v2/login'); }
            }
            //  Load the manage page.
            else if (substr($url, 0, 7) === '/manage' && strlen($url) === 7)
            {
                //
                header('Content-Type: text/html');
                echo file_get_contents(self::$manage);
            }
            else
            {
                //  If the route was not defined above, serve the index page.
                header('Content-Type: text/html');
                echo file_get_contents(self::$index);
            }

            //echo 'NOTE: GET has not yet been fully implemented.<br>';
        }

        function post ($url)
        {
            $data = json_decode(file_get_contents('php://input'));

            if (substr($url, 0, 6) === '/login')
            {
                require_once 'app/library/socket/JSON_socket.php';
                require_once 'app/library/socket/MySQL_socket.php';

                require_once 'app/models/user/user_model.php';
                require_once 'app/controllers/new_users.php';

                require_once 'app/library/plugin/jwt/JWT.php';
                require_once 'app/models/user/token_model.php';
                require_once 'app/library/socket/activeUser_socket.php';

                $decoded_login = (object)[
                    'username' => base64_decode($data->username),
                    'password' => base64_decode($data->password)
                ];

                //  NOTE:   $is_user should ba an object with {id, username, hash, type}
                $user = new User();
                $is_user = $user->authenticate($decoded_login);

                $res = (object)['success' => false];

                if ($is_user)
                {
                    $JWT_handler = new activeUser_socket();
                    $token = $JWT_handler->create($is_user);
                    if ($token)
                    {
                        $res->success = true;
                        $res->token = $token->toJSON();
                    }
                    else if (strpos($JWT_handler->error, 'already active'))
                    {
                        $res->error = 'You are already logged in.';
                    }
                    else
                    {
                        $res->error = 'An error occured, please try again later.';
                    }
                }
                else
                {
                    $res->error = 'Wrong username or password.';
                }

                header("Content-Type: application/json");
                echo json_encode($res);
            }
            else if (substr($url, 0, 7) === '/logout')
            {
                require_once 'app/library/socket/JSON_socket.php';
                require_once 'app/library/socket/MySQL_socket.php';

                require_once 'app/models/user/user_model.php';
                require_once 'app/controllers/new_users.php';

                require_once 'app/library/plugin/jwt/JWT.php';
                require_once 'app/models/user/token_model.php';
                require_once 'app/library/socket/activeUser_socket.php';

                $res = (object)['success' => false, 'error' => 'Unable to parse token.'];
                $token;
                try
                {
                    $json = json_decode($data);
                    $token = new Token($json->id, $json->token, $json->timestamp);
                    $active_user = new activeUser_socket();
                    $res->success = $active_user->delete($token);

                    if (!$res->success)
                    {
                        $res->error = $active_user->error;
                    }
                    else
                    {
                        $res->success = true;
                        unset($res->error);
                    }
                }
                catch (Exception $e) { self::$logger->log(self::$logName, $e); }

                header("Content-Type: application/json");
                echo json_encode($res);
            }

            //  Logout

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

        private function is_loggedin()
        {

        }

    }
?>
