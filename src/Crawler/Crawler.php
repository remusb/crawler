<?php
namespace Crawler;

use Common\Config;
use Common\Queue;
use Goutte\Client;
use Common\Util;

class Crawler {
	static protected $me = null;

	protected $shouldListen = true;
	protected $id = 0;

	public static function Instance() {
		if (self::$me == null) {
			self::$me = new Crawler();
		}

		return self::$me;
	}

    private function __construct() {
    	// $queue = Queue::Instance()->db();
    	// $this->id = $queue->incr('crawlers');
    }

    public function run() {
        $queue = Queue::Instance()->db();
        $result = array();

        // $this->setShouldListen(1);

        // while ($this->shouldListen) {
            $result = $queue->brPop('queue_pending', 0);

            $this->fetchDocument($result[1]);

            // $this->shouldListen = $this->getShouldListen();
        // }

        $queue->close();
    }

    public function getShouldListen() {
        $queue = Queue::Instance()->db();
        $key = 'Crawler' + $this->id + 'Listening';

        return (bool) $queue->hGet('settings', $key);
    }

 	public function setShouldListen($shouldListen) {
        $queue = Queue::Instance()->db();
        $key = 'Crawler' . $this->id . 'Listening';

        $queue->hSet('settings', $key, $shouldListen);
    }

    protected function fetchDocument($url) {
        echo "Downloading document {$url} ...\n";

        // Request
        $client = new Client();
        $client->setHeader('User-Agent', "price.hunter[at]bunduc.ro/Price-0.1-dev");

        $crawler = $client->request('GET', $url);

        //Response
        $statusCode = $client->getResponse()->getStatus();

        // TODO: Improve this
        if ($statusCode == 200) {
            $contentType = $client->getResponse()->getHeader('Content-Type');

            if (strpos($contentType, 'text/html') !== false) {
                $contextLinks = array();

                $crawler = $crawler->filter('a')->each(function(\Symfony\Component\DomCrawler\Crawler $node, $i) use ($url, &$contextLinks) {
                    $newUrl = Util::Instance()->sanitizeUrl($node->link()->getUri());

                    if (in_array($newUrl, $contextLinks)) {
                        return false;
                    }

                    if (!Util::Instance()->isValidUrl($newUrl, $url)) {
                        return false;
                    }

                    $contextLinks[$i] = $newUrl;

                    return true;
                });

                if (count($contextLinks) > 0) {
                    $this->returnNewLinks($contextLinks);
                }
            }
        }
    }

    protected function returnNewLinks($urlArray) {
        echo "Queueing " . count($contextLinks) . " discovered URL(s)...\n";
        
        $client = new Client();
        $managerEndpoint = Config::Instance()->get('manager', 'endpoint');

        $crawler = $client->request('POST', $managerEndpoint . 'Explorer/addUrl.json', array(), array(), array(), json_encode($urlArray));
        $response = json_decode( $client->getResponse()->getContent(), true );

        var_dump($response);
    }
}