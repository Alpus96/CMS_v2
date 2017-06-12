<?php
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

        public function confirm ($username, $key, $ip)
        {
            $connection = parent::connect();
            if ($qurey = $connection->prepare('SELECT * FROM active WHERE username = ?, key = ?, ip = ? LIMIT 1'))
            {
                $query->bind_param('sss', $username, $key, $ip);
                $query->execute();

                $query->bind_result($username);
                $query->fetch();

                $query->close();
                $connection->close();

                if ($username)
                {
                    return $username;
                }
                return false;
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