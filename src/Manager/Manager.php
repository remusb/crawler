<?php
namespace Manager;

use Common\Queue;

class Manager {
	static protected $me = null;

	public static function Instance() {
		if (self::$me == null) {
			self::$me = new Manager();
		}

		return self::$me;
	}

    private function __construct() {}

    public function run() {
        $queue = Queue::Instance()->db();
        $result = '';
        
        $result = $this->processUrlBatch();

        $queue->close();

        return $result;
    }

    public function processUrlBatch() {
    	$queue = Queue::Instance()->db();
    	$currentTime = time();
        $lua = <<<LUA
local links = redis.call("ZRANGEBYSCORE", KEYS[1], 0, ARGV[1], 'LIMIT', 0, 1000)
if table.maxn(links) == 0 then
    return links
end
redis.call("ZREMRANGEBYSCORE", KEYS[1], 0, ARGV[1])
redis.call("LPUSH", KEYS[2], unpack(links))
return links
LUA;

        $urlList = $queue->eval($lua, array('queue', 'queue_pending', time()), 2);

        if ($urlList === false) {
            $urlList = $queue->getLastError();
        }

        return count($urlList);
    }

    public function clearQueue() {
        $queue = Queue::Instance()->db();

        return $queue->del('queue');
    }

    public function clearQueuePending() {
        $queue = Queue::Instance()->db();

        return $queue->del('queue_pending');
    }

    public function addBlacklist($entries) {
        $queue = Queue::Instance()->db();
        array_unshift($entries, 'urlBlackList');
        $cnt = 0;

        $cnt = call_user_func_array(array($queue, 'sAdd'), $entries);

        return 'Added ' . $cnt . ' items';
    }

    public function removeBlacklist($entries) {
        $queue = Queue::Instance()->db();
        array_unshift($entries, 'urlBlackList');
        $cnt = 0;

        $cnt = call_user_func_array(array($queue, 'sRem'), $entries);

        return 'Removed ' . $cnt . ' items';
    }
}