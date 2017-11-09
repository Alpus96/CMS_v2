<?php

    require_once ROOT_PATH.'/sbin/lib/opt/socket/json.php';
    require_once ROOT_PATH.'/sbin/app/proc/users/user.php';
    require_once ROOT_PATH.'/sbin/app/proc/users/admin.php';
    //  ...

    /**
     *  TODO: Finish this class, review code and write comments.
     * 
     *  TODO: Implement use of this class in the response class.
     */

    class ResponseHelper {

        public function __construct () {

        }

        protected function isUser ($token) {
            $user = new User();
            if ($token) {
                $new_token = $user->verifyToken($token);
                if ($new_token) {
                    self::setNewTokenCookie($new_token);
                    return true;
                }
            }
            return false;
        }

        protected function isAdmin ($token) {
            if ($token) {
                $admin = new Admin($token);
                return $admin->isAdmin();
            }
            return false;
        }

        protected function setNewTokenCookie ($new_token) {
            $token_config = self::$json->read('token_config');
            $expires = time() + $token_config->valid_for;
            $cookie_obj = (object)[
                'value' => $new_token,
                'expires' => null,
                'fallbacktime' => $expires
            ];
            setcookie('token', json_encode($cookie_obj), $expires);
        }

        protected function addAdminSettings ($view) {
            if (!is_string($view)) { return false; }
            $admin_settings = self::$json->read('admin_settings');
            return str_replace($admin_settings->tags, $admin_settings->parts, $view);
        }

        protected function editable ($view) {
            if (!is_string($view)) { return false; }
            $editable_partials = self::$json->read('editable_partials');
            return str_replace($editable_partials->tags, $editable_partials->parts, $view);
        }
    }

?>