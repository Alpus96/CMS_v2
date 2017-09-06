<?php
    /**
     *  TODO:   Write comments.
     */
    class JSON_Socket
    {
        static private $libraryPath = 'library/';

        function createFile ($name)
        {
            $path = $this->libraryPath.$name.'.json';
            if (!file_exists($path))
            {
                return touch($path);
            }
            return false;
        }

        function readFile ($name)
        {
            $path = $this->libraryPath.$name.'.json';
            if (file_exists($path))
            {
                $jsonfile = file_get_contents($path);
                return json_decode($jsonfile);
            }
            return false;
        }

        function updateFile ($name, $data)
        {
            $path = $this->libraryPath.$name.'.json';
            if (file_exists($path))
            {
                return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
            }
            return false;
        }

        function deleteFile ($name)
        {
            $path = $this->libraryPath.$name.'.json';
            if (file_exists($path))
            {
                return unlink($path);
            }
            return 0;
        }

    }
?>