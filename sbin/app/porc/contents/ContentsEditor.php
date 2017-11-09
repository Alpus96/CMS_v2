<?php
    /**
    *   This class handles changing information in the CONTENTS table.
    *
    *   @method     __construct($token):
    *
    *   @method     createContents($newContents):
    *
    *   @method     getAsMD($id):
    *
    *   @method     updateContents($contents):
    *
    *   @method     deleteContents($id):
    *
    *   @method     databaseError($error)   Logs the passed connection $error.
    *
    *   @uses       MySQL_socket:           Reads database credentials and
    *                                       has a method to connect to the database.
    *
    *   @uses       logger:                 To log errors id 'app/library/debug/logs/*.txt'
    *
    *   @uses       Tokens:                 To verify the user is logged in and has permissions.
    *
    *   @throws     Exception:              Unable to connect to database: $error.
    * */
    require_once 'app/library/debug/logger.php';
    require_once 'app/objects/users/Tokens.php';
    require_once 'app/library/plugin/parsedown/Parsedown.php';

    class ContentsEditor extends MySQL_socket {

        static private $logger;
        static private $logName;

        static private $token;

        static private $getMDQuery;
        static private $updateContentsQuery;
        static private $createContentsQuery;
        static private $getForDeleteQuery;
        static private $insertInDeleteQuery;
        static private $deleteFromContentsQuery;

        function __construct ($token) {
            parent::__construct();

            self::$logger = new logger();
            self::$logName = '_contentsEditor_log';

            self::$token = new Token($token);

            self::$getMDQuery = 'SELECT CONTENT_TEXT FROM CONTENTS WHERE ID = ?';
            self::$updateContentsQuery = 'UPDATE CONTENTS SET CONTENT_TEXT = ? WHERE ID = ?';
            self::$createContentsQuery = 'INSERT INTO CONTENTS SET CONTENT_TEXT = ?, MARKER = ?, AUTHOR = ?';
            self::$getForDeleteQuery = 'SELECT CONTENT_TEXT, MARKER FROM CONTENTS WHERE ID = ?';
            self::$insertInDeleteQuery = 'INSERT INTO DELETED_CONTENTS SET CONTENT_TEXT = ?, MARKER = ?';
            self::$deleteFromContentsQuery = 'DELETE FROM CONTENTS WHERE ID = ?';
        }

        function createContents ($newContents) {
            if (self::$token->toJSON() && property_exists($newContents, 'text') && $newContents->text != '' && property_exists($newContents, 'marker') && $newContents->marker != '') {
                $connObj = parent::connect();
                if (!$connObj->error) {
                    $connection = $connObj->connection;
                    if ($query = $connection->prepare(self::$createContentsQuery)) {
                        $authName = self::$token->decodedToken()->authName;
                        $query->bind_param('sss', $newContents->text, $newContents->marker, $authName);
                        $query->execute();
                        $result = $query->affected_rows > 0 ? true : false;
                        $query->close();
                        $connection->close();
                        return $result;
                    }
                } else { self::databaseError($connObj->connection); }
            }
            return false;
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
            if (self::$token->toJSON() && is_numeric($id)) {
                $connObj = parent::connect();
                if (!$connObj->error) {
                    $connection = $connObj->connection;
                    if ($query = $connection->prepare(self::$getForDeleteQuery)) {
                        $query->bind_param('i', $id);
                        $query->execute();
                        $query->bind_result($text, $marker);
                        $query->fetch();
                        $query->close();
                        if (!$text || !$marker) {
                            $connection->close();
                            return false;
                        }
                        if ($query = $connection->prepare(self::$insertInDeleteQuery)) {
                            $query->bind_param('ss', $text, $marker);
                            $query->execute();
                            $result = $query->affected_rows > 0 ? true : false;
                            $query->close();
                            if (!$result) {
                                $connection->close();
                                return false;
                            }
                            if ($query = $connection->prepare(self::$deleteFromContentsQuery)) {
                                $query->bind_param('i', $id);
                                $query->execute();
                                $result = $query->affected_rows > 0 ? true : false;
                                $query->close();
                                $connection->close();
                                return $result;
                            }
                        }
                    }
                } else { self::databaseError($connObj->connection); }
            }
            $connection->close();
            return false;
        }

        private static function databaseError($error) {
            $msg = 'Unable to connect to the database : '.$error;
            self::$logger->log(self::$logName, $msg);
            throw new Exception($msg);
        }

    }
?>