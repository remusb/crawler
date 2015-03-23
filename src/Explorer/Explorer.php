<?php
namespace Explorer;

use Common\Config;
use Common\Queue;
use Common\Storage;
use Common\Util;

class Explorer {
	static protected $me = null;

    protected $blackList = array();

	public static function Instance() {
		if (self::$me == null) {
			self::$me = new Explorer();
		}

		return self::$me;
	}

    private function __construct() {
        $this->refreshBlacklist();
    }

    public function addToQueue($url, $timestamp = 0) {
        $queue = Queue::Instance()->db();

        $queue->zadd('queue', $timestamp, $url);
    }

    public function addUrlList($urlList, $timeout = 0) {
    	$defaultTimeout = Config::Instance()->get('frequency', 'crawling');
    	$db = Storage::Instance()->db();

    	$db->beginBatch();

    	foreach ($urlList as $seedUrl) {
    		$url = parse_url($seedUrl);
            $sanitizedUrl = Util::Instance()->sanitizeUrl($seedUrl);
            $blackListed = false;

            foreach ($this->blackList as $blackUrlPattern) {
                if (stripos($sanitizedUrl, $blackUrlPattern) !== false) {
                    $blackListed = true;
                    break;
                }
            }

            if ($blackListed) {
                continue;
            }

    		// try to make some sense from an invalid/partial link
    		if (!array_key_exists('host', $url)) {
    			$url['host'] = $seedUrl;
    		}

    		// try to make some sense from an invalid/partial link
    		if (!array_key_exists('scheme', $url)) {
    			$url['scheme'] = 'http';
    		}

			$db->query(
				'INSERT INTO "domain" ("domain", "homepage") VALUES (:domain, :homepage);',
				[
					'domain' => str_replace('www.', '', $url['host']),
					'homepage' => $url['scheme'] . '://' . $url['host']
				]
			);

			$db->query(
				'INSERT INTO "url" ("url", "frequency") VALUES (:url, :frequency);',
				[
					'url' => $sanitizedUrl,
					'frequency' => $defaultTimeout
				]
			);

            $this->addToQueue($sanitizedUrl, $timeout + $defaultTimeout);
    	}

    	$result = $db->applyBatch();
    }

    public function readInitialSeed() {
    	$seedArray = $this->getInitialSeed();
    	$defaultTimeout = Config::Instance()->get('frequency', 'crawling');

    	$this->addUrlList($seedArray);
    }

    public function getInitialSeed() {
    	return Config::Instance()->getArray('seed');
    }

    protected function refreshBlacklist() {
        $queue = Queue::Instance()->db();

        $this->blackList = $queue->sMembers('urlBlackList');
    }

    public function getBlacklist() {
        return $this->blackList;
    }
}