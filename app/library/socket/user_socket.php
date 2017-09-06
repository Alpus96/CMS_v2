<?php
    /**
    *   TODO:   Convert into using JWT in cookies.
    */
    class ActiveUsers extends MySQL_Socket
    {
        public function __construct ()
        { parent::__construct(); }

        public function add ($username, $key, $ip)
        {
            $connection = parent::connect();
            if ($qurey = $connection->prepare('INSERT INTO active SET ?'))
            {
                $query->bind_param('s', (object)[
                    'username' => $username,
                    'key' => $key,
                    'ip' => $ip
                ]);
                $query->execute();

                $query->bind_result($success);
                $query->fetch();

                $query->close();
                $connection->close();
                return $success;
            }
            $connection->close();
            return false;
        }

        public function fetch ($key)
        {
            $connection = parent::connect();
            if ($qurey = $connection->prepare('SELECT * FROM active WHERE key = ? LIMIT 1'))
            {
                $query->bind_param('s', $key);
                $query->execute();

                $query->bind_result($activeUser);
                $query->fetch();

                $query->close();
                $connection->close();
                return $activeUser;
            }
            $connection->close();
            return false;
        }

        public function update ($username, )
        {

        }

        public function remove ($id)
        {

        }
    }
?>