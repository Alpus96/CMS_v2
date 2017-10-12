<?php
    require_once 'app/library/socket/MySQL_socket.php';
    require_once 'app/library/debug/logger.php';
    require_once 'app/objects/users/Tokens.php';

    class RemovedContents extends MySQL_socket {

        static private $logger;
        static private $logName;

        static private $token;

        static private $getRemovedQuery;
        static private $getByIdQuery;
        static private $restoreToContentsQuery;
        static private $deleteOldQuery;

        public function __construct ($token) {
            parent::__construct();

            self::$logger = new logger();
            self::$logName = '_removedContents_errorLog';

            self::$token = new Token($token);

            self::$getRemovedQuery = 'SELECT * FROM DELETED_CONTENTS';
            self::$getByIdQuery = 'SELECT CONTENT_TEXT, MARKER FROM DELETED_CONTENTS WHERE ID = ?';
            self::$restoreToContentsQuery = 'INSERT INTO CONTENTS SET CONTENT_TEXT = ?, MARKER = ?, AUTHOR = ?';
            self::$deleteOldQuery = 'DELETE FROM DELETED_CONTENTS WHERE ID = ?';
        }

        public function getRemovedContents () {
            if (self::$token->toJSON()) {
                $connObj = parent::connect();
                if (!$connObj->error) {
                    $connection = $connObj->connection;
                    if ($result = $connection->query(self::$getRemovedQuery)) {
                        $data = [];
                        while ($row = $result->fetch_object()) {
                            $deleted = strtotime($row->DELETED);
                            if (($deleted+(3*24*60*60)) > Time()) {
                                array_push($data, $row);
                            } else {
                                $res = self::deleteOld($row->ID);
                                if (!$res) {
                                    self::$logger->log(self::$logName, 'Could not delete old contents with id '.$row->ID.'.');
                                }
                            }
                        }
                        $connection->close();
                        return $data;
                    } else { $connection->close(); }
                } else { self::databaseError($connObj->connection); }
            }
            return false;
        }

        public function restoreContents ($id) {
            if (is_numeric($id) && self::$token->toJSON()) {
                $c = self::getById($id);
                $authName = self::$token->decodedToken()->authName;
                $connObj = parent::connect();
                if (!$connObj->error) {
                    $connection = $connObj->connection;
                    if ($query = $connection->prepare(self::$restoreToContentsQuery)) {
                        $query->bind_param('sss', $c->text, $c->marker, $authName);
                        $query->execute();
                        $success = $query->affected_rows > 0 ? true : false;
                        $query->close();
                        $connection->close();
                        if ($success) {
                            $del = self::deleteOld($id);
                            if (!$del) {
                                self::$logger->log(self::$logName, 'Could not delete restored row from deleted contents. (id: '.$id.')');
                            }
                        }
                        return $success;
                    } else { $connection->close(); }
                } else { self::databaseError($connObj->connection); }
            }
            return false;
        }

        private function deleteOld ($id) {
            if (is_numeric($id)) {
                $connObj = parent::connect();
                if (!$connObj->error) {
                    $connection = $connObj->connection;
                    if ($query = $connection->prepare(self::$deleteOldQuery)) {
                        $query->bind_param('i', $id);
                        $query->execute();
                        $success = $query->affected_rows > 0 ? true : false;
                        $query->close();
                        $connection->close();
                        return $success;
                    } else { $connection->close(); }
                } else { self::databaseError($connObj->connection); }
            }
            return false;
        }

        private function getById ($id) {
            if (is_numeric($id)) {
                $connObj = parent::connect();
                if (!$connObj->error) {
                    $connection = $connObj->connection;
                    if ($query = $connection->prepare(self::$getByIdQuery)) {
                        $query->bind_param('i', $id);
                        $query->execute();
                        $query->bind_result($text, $marker);
                        $query->fetch();
                        $query->close();
                        $connection->close();
                        return (object)['text' => $text, 'marker' => $marker];
                    } else { $connection->close(); }
                } else { self::databaseError(); }
            }
            return false;
        }

        private static function databaseError($error) {
            $msg = 'Unable to connect to the database : '.$error;
            self::$logger->log(self::$logName, $msg);
            throw new Exception($msg);
        }

    }
?>