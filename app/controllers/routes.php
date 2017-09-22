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

        private static $logger;
        private static $logName;

        function __construct () {
            self::$index = dirname(dirname(__FILE__)).'/views/index.html';
            self::$login = dirname(dirname(__FILE__)).'/views/login.html';
            self::$manage = dirname(dirname(__FILE__)).'/views/manage.html';

            require_once '/users/alpus96/sites/projects/CMS_v2/app/library/debug/logger.php';
            self::$logger = new logger();
            self::$logName = '_routesLog';
        }

        function get ($url)
        {
            //  NOTE:   The CMS be a subdomain to the "pages"?

			//	Load login page
            if (substr($url, 0, 1) === '/' && strlen($url) === 1) {
                header('Content-Type: text/html');
                echo file_get_contents(self::$index);
            }
            //	Load post(s)
            else if (substr($url, 0, 6) === '/post/')
            {
                // tag or specific
            }
            //	Load article(s)
            else if (substr($url, 0, 9) === '/article/')
            {
                // tag or specific
            }
			//	Load image post(s)
            else if (substr($url, 0, 11) === '/imagepost/')
            {
                // tag or specific
            }
			//	Load Image(s)
            else if (substr($url, 0, 7) === '/image/')
            {

            }
            //  Load the login page.
            else if (substr($url, 0, 6) === '/login' && strlen($url) === 6)
            {
                //
                header('Content-Type: text/html');
                echo file_get_contents(self::$login);
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

            echo 'NOTE: GET has not yet been fully implemented.<br>';
        }

        function post ($url)
        {
            $data = json_decode(file_get_contents('php://input'));
            $data->username = base64_decode($data->username);
            $data->password = base64_decode($data->password);
            $res = (object)[
                'success' => true,
                'data' => $data
            ];
            $res = json_encode($res);
            header('Content-Type: text/javascript');
            echo $res;

            /*if (substr($url, 0, 6) === '/login') {
                $res = (object)[
                    'success' => true,
                    'data' => $_POST
                ];
                self::$logger->log(
                    self::$logName,
                    '$res : '.json_encode($res)
                );
                echo json_encode($res);
            }*/

            //  Login
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

        private function req ($files)
        {

        }
    }
?>
