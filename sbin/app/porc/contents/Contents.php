<?php
    /**
    *   This class handles reading from the CONTENTS table in the database.
    *
    *   @method     __construct():          Constructs MySQL_socket and logger for later use.
    *                                       Also sets the prepared query strings to use in the
    *                                       class methods.
    *
    *   @method     getByMarker($option):   Gets all rows in the CONTENTS table acording to
    *                                       the passed options in $options.
    *
    *   @method     getById($option):       Gets the information with id acording to
    *                                       the passed options in $option.
    *
    *   @method     databaseError($error)   Logs the passed connection $error.
    *
    *   @uses       MySQL_socket:           Reads database credentials and
    *                                       has a method to connect to the database.
    *
    *   @uses       logger:                 To log errors id 'app/library/debug/logs/*.txt'
    *
    *   @uses       Parsedown:              To parse contents text from markdown to html before
    *                                       returning contents data.
    *
    *   @throws     Exception:              Unable to connect to database: $error.
    * */

    require_once 'app/library/socket/MySQL_socket.php';
    require_once 'app/library/debug/logger.php';
    require_once 'app/library/plugin/parsedown/Parsedown.php';

    class Contents extends MySQL_socket {

        static private $logger;
        static private $logName;

        static private $getByMarkerQuery;
        static private $getByIDQuery;

        function __construct() {
            parent::__construct();

            self::$logger = new logger();
            self::$logName = '_contents_errorLog';

            self::$getByMarkerQuery = 'SELECT ID, CONTENT_TEXT, AUTHOR, TIMESTAMP_CREATED, TIMESTAMP_EDITED FROM CONTENTS WHERE MARKER = ? ORDER BY TIMESTAMP_CREATED DESC LIMIT ?,?';
            self::$getByIDQuery = 'SELECT CONTENT_TEXT, AUTHOR, TIMESTAMP_CREATED, TIMESTAMP_EDITED FROM CONTENTS WHERE ID = ?';
        }

        function getByMarker ($options) {
            if (!property_exists($options, 'marker') || !property_exists($options, 'amount')) {
                self::$logger->log(self::$logName, 'Faulty content request: '.json_encode($options));
                return false;
            }
            $connObj = parent::connect();
            if (!$connObj->error) {
                $connection = $connObj->connection;
                if ($query = $connection->prepare(self::$getByMarkerQuery)) {
                    $offset = 0;
                    if (property_exists($options, 'offset') && $options->offset >= 0)
                    { $offset = $options->offset; }
                    $amount = 10000;
                    if (property_exists($options, 'amount') && $options->amount > 0)
                    { $amount = $options->amount; }
                    $query->bind_param('sii', $options->marker, $offset, $amount);
                    $query->execute();
                    $query->bind_result($id, $text, $author, $created, $edited);
                    $contents = [];
                    while ($query->fetch()) {
                        array_push($contents, (object)['id' => $id, 'text' => $text, 'author' => $author, 'created' => $created, 'edited' => $edited]);
                    }
                    $query->close();
                    $connection->close();

                    $down_parser = new Parsedown();
                    foreach ($contents as $key => $content) {
                        $content->text = $down_parser->text($content->text);
                        if (!property_exists($options, 'incAuth') || $options->incAuth == false) {
                            unset($content->author);
                        }
                        if (!property_exists($options, 'incDate') || $options->incDate == false) {
                            unset($content->created);
                            unset($content->edited);
                        }
                        $contents[$key] = $content;
                    }
                    $res = (object)['more' => false, 'entries' => $contents];
                    if (count($contents) > $amount) {
                        $res->more = true;
                        array_pop($res->entries);
                    }
                    return $res;
                }
                $connection->close();
            } else { self::databaseError($connObj->connection); }
            return false;
        }

        function getId ($options) {
            if (!property_exists($options, 'id')) {
                self::$logger->log(self::$logName, 'Faulty content request: '.json_encode($options));
                return false;
            }
            $connObj = parent::connect();
            if (!$connObj->error) {
                $connection = $connObj->connection;
                if ($query = $connection->prepare(self::$getByIDQuery)) {
                    $query->bind_param('i', $options->id);
                    $query->execute();
                    $query->bind_result($text, $author, $created, $edited);
                    $contents = [];
                    while ($query->fetch()) {
                        array_push(
                            $contents,
                            [
                                'text' => $text,
                                'author' => $author,
                                'created' => $created,
                                'edited' => $edited
                            ]
                        );
                    }
                    $query->close();
                    $connection->close();

                    foreach ($contents as $key => $content) {
                        if (!property_exists($options, 'incAuth') || $options->incAuth == false) {
                            unset($content->author);
                        }
                        if (!property_exists($options, 'incDate') || $options->incDate == false) {
                            unset($content->created);
                            unset($content->edited);
                        }
                    }
                    return $contents;
                }
                $connection->close();
            } else { self::databaseError($connObj->connection); }
            return false;
        }

        private static function databaseError($error) {
            $msg = 'Unable to connect to the database : '.$error;
            self::$logger->log(self::$logName, $msg);
            throw new Exception($msg);
        }

    }
?>