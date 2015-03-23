<?php
namespace Common;

use \evseevnn\Cassandra\Database;

class Storage {
	protected $database;

	static protected $me = null;
	public static function Instance() {
		if (self::$me == null) {
			self::$me = new Storage();
		}

		return self::$me;
	}

    private function __construct() {
    	$keyspace = Config::Instance()->getConfig('keyspace', 'cassandra');
    	$nodes = Config::Instance()->getConfig('nodes', 'cassandra');

    	$this->database = new Database($nodes, $keyspace);
    	$this->database->connect();
    }

    public function db() {
    	return $this->database;
    }

}