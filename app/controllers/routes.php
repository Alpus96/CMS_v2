<?php
    /*  TODO:   Require all files in the project. (library, models, views,
    *           controllers in that order)
    *
    *   TODO:   Complete routes to define the structure and implementation of
    *           the porject.
    * */
    class Router
    {
        function get ($url)
        {
            echo 'NOTE: GET has not yet been fully implemented.<br>';
            //  NOTE:   The CMS be a subdomain to the "pages"?

			//	Load login page
            if (substr($url, 0, 6) === '/login')
            {
                //  TODO: Load login html here.
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
            //  Load manage page.
            else if (substr($url, 0, 7) === '/manage')
            {
                //
            }
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
