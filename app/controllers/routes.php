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
    class Router
    {
        private static $index;
        private static $login;
        private static $manage;

        function __construct () {
            self::$index = dirname(dirname(__FILE__)).'/views/index.html';
            self::$login = dirname(dirname(__FILE__)).'/views/login.html';
            self::$manage = dirname(dirname(__FILE__)).'/views/manage.html';
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
            else if (substr($url, 0, 6) === '/login')
            {
                //
                header('Content-Type: text/html');
                echo file_get_contents(self::$login);
            }
            //  Load the manage page.
            else if (substr($url, 0, 7) === '/manage')
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
			echo 'NOTE: POST has not yet been implemented.<br>';

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
        }

        private function req ($files)
        {

        }
    }
?>
