<?php

    require_once 'app/library/debug/logger.php';
    require_once 'app/objects/users/Tokens.php';
    require_once 'app/library/plugin/parsedown/Parsedown.php';

    class ContentsEditor extends MySQL_socket {

        static private $logger;
        static private $logName;

        static private $token;

        static private $getMDQuery;
        static private $updateContentsQuery;

        function __construct ($token) {
            parent::__construct();

            self::$logger = new logger();
            self::$logName = '_contentsEditor_log';

            self::$token = new Token($token);

            self::$getMDQuery = 'SELECT CONTENT_TEXT FROM CONTENTS WHERE ID = ?';
            self::$updateContentsQuery = 'UPDATE CONTENTS SET CONTENT_TEXT = ? WHERE ID = ?';
        }

        function createContents ($newContents) {

        }

        function getAsMD ($id) {
            if (self::$token->toJSON() && is_numeric($id)) {
                $connObj = parent::connect();
                if (!$connObj->error) {
                    $connection = $connObj->connection;
                    if ($query = $connection->prepare(self::$getMDQuery)) {
                        $query->bind_param('i', $id);
                        $query->execute();
                        $query->bind_result($markdown);
                        $query->fetch();
                        $query->close();
                        $connection->close();
                        return $markdown;
                    }
                } else { self::databaseError($connObj->connection); }
            }
            return false;
        }

        function updateContents ($contents) {
            if (self::$token->toJSON() && property_exists($contents, 'id') && property_exists($contents, 'newText')) {
                $connObj = parent::connect();
                if (!$connObj->error) {
                    $connection = $connObj->connection;
                    if ($query = $connection->prepare(self::$updateContentsQuery)) {
                        $query->bind_param('si', $contents->newText, $contents->id);
                        $query->execute();
                        $res = $query->affected_rows > 0 ? true : false;
                        $query->close();
                        $connection->close();
                        if ($res) {
                            $down_parser = new Parsedown();
                            return $down_parser->text($contents->newText);
                        }
                    }
                } else { self::databaseError($connObj->connection); }
            }
            return false;
        }

        function deleteContents ($id) {

        }

        private static function databaseError($error) {
            $msg = 'Unable to connect to the database : '.$error;
            self::$logger->log(self::$logName, $msg);
            throw new Exception($msg);
        }

    }
?>