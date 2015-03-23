<?php
namespace Common;

class Queue {
	protected $database;
    protected $sub = null;

	static protected $me = null;
	public static function Instance() {
		if (self::$me == null) {
			self::$me = new Queue();
		}

		return self::$me;
	}

    private function __construct() {
        $this->database = $this->setup();
    }

    public function setup() {
        $socket = Config::Instance()->getConfig('socket', 'redis');
        $host = Config::Instance()->getConfig('host', 'redis');
        $port = Config::Instance()->getConfig('port', 'redis');

        $result = false;

        $db = new \Redis();

        try {
            if (strlen($socket) > 0) {
                $db->connect($socket);
            } else {
                $db->connect($host, $port);
            }

            $db->select(0);

            $result = true;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        
        if (!$result) {
            // TODO: Handle exception
        }

        return $db;
    }

    public function db() {
    	return $this->database;
    }
}