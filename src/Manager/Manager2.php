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
        $queue = Queue::Instance()->sub();
        $result = '';

        $pubsub = $queue->pubSubLoop();
        $pubsub->subscribe('__keyspace@0__:queue', 'control_manager');

        foreach ($pubsub as $message) {
            switch ($message->kind) {
                case 'subscribe':
                    echo "Subscribed to {$message->channel}\n";

                    break;
                case 'message':
                    if ($message->channel == 'control_manager') {
                        if ($message->payload == 'quit') {
                            echo "Aborting manager process...\n";
                            $pubsub->unsubscribe();
                        }
                    } else if (strtolower($message->payload) == 'zadd') {
                        $result = $this->processUrlBatch();
                        if (is_string($result)) {
                            echo "Issue processing: {$result}\n";
                        } else {
                            echo "Processed {$result} URL(s)\n";
                        }
                    }

                    break;
            }
        }

        unset($pubsub);
        $queue->quit();
    }

    public function processUrlBatch() {
    	$queue = Queue::Instance()->db();

    	$currentTime = time();
        $urlList = $queue->moveUrl('queue', 'queue_pending', time());

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