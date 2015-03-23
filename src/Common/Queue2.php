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

    protected function setup($parameters = array()) {
        $socket = Config::Instance()->getConfig('socket', 'redis');
        $host = Config::Instance()->getConfig('host', 'redis');
        $port = Config::Instance()->getConfig('port', 'redis');

        $result = false;

        try {
            if (strlen($socket) > 0) {
                $db = new \Predis\Client(array(
                    'scheme' => 'unix',
                    'path' => $socket,
                    // 'database' => 0
                ) + $parameters);

               $result = true;
            } else {
                $db = new \Predis\Client(array(
                    'scheme' => 'tcp',
                    'host' => $host,
                    'port' => $port,
                    // 'database' => 0
                ) + $parameters);

               $result = true;
            }
        } catch (Exception $e) {
            $result = false;
            echo $e->getMessage();
        }
        
        if (!$result) {
            // TODO: Handle exception
        }

        $db->getProfile()->defineCommand('moveUrl', 'Common\\Redis\\MoveUrlScript');

        return $db;
    }

    public function db() {
    	return $this->database;
    }

    public function sub() {
        if ($this->sub == null) {
            $this->sub = $this->setup(array(
                'timeout' => 600,
                'read_write_timeout' => 0
            ));
        }

        return $this->sub;
    }
}