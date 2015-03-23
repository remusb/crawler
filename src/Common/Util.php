<?php
namespace Common;

class Util {
	static protected $me = null;

	public static function Instance() {
		if (self::$me == null) {
			self::$me = new Util();
		}

		return self::$me;
	}

	private function __construct() { }

	public function sanitizeUrl($url) {
		$un = new \URL\Normalizer( $url );

		return $un->normalize();
	}

    public function isValidUrl($url, $currentUrl = '') {
        if (empty($url)) {
            return false;
        }

        // the new url must not be the same with the current one
        if (strtolower($url) == strtolower($currentUrl)) {
            return false;
        }

        // search for invalid patterns
        $invalidLinks = array(
            '@^javascript\:.*@',
            '@^mailto\:.*@',
            '@^#.*@'
        );

        foreach ($invalidLinks as $pattern) {
            if (preg_match($pattern, $url)) {
                return false;
            }
        }

        // the new url must be in the same domain as the current one
        if (strlen($currentUrl) > 0) {
            $parsedUrl = parse_url($url, PHP_URL_HOST);
            $parsedCurrentUrl = parse_url($currentUrl, PHP_URL_HOST);

            if ($parsedUrl == null || $parsedCurrentUrl == null || $parsedUrl != $parsedCurrentUrl) {
                return false;
            }
        }

        return true;
    }
}