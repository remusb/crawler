<?php
namespace Common;

use Common\Config\YamlConfigLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Definition\Processor;

class Config {
	protected $locator;
	protected $loader;
	protected $cache = array();
	protected $arrayCache = array();

	static protected $me = null;

	public static function Instance() {
		if (self::$me == null) {
			self::$me = new Config();
		}

		return self::$me;
	}

	private function __construct() { }

	public function setup($configDir) {
		$directories = array($configDir);
		$this->locator = new FileLocator($directories);

		// convert the config file into an array
		$this->loader = new YamlConfigLoader($this->locator);

		return $this;
	}

	public function loadYaml($scope) {
		$configValues = $this->loader->load($this->locator->locate($scope . '.yml'));
		$fqn = 'Common\Config\\' . ucfirst($scope);

		// process the array using the defined configuration
		$processor = new Processor();
		$configuration = new $fqn();
		$processedConfiguration = array();

		try {
		    $processedConfiguration = $processor->processConfiguration(
		        $configuration,
		        $configValues
		    );
		} catch (Exception $e) {
		    // validation error
		    echo $e->getMessage() . PHP_EOL;
		}

		$this->cache[$scope] = $processedConfiguration;

		return $this->cache[$scope];
	}

	public function loadGroup($scope) {
		$db = Storage::Instance()->db();
		$configuration = $db->query('SELECT * FROM "configuration" WHERE "env" = :env and "group" = :scope', ['env' => 'dev', 'scope' => $scope]);

		try {
		    $processedConfiguration = $configuration[0]['config'];
		    $arrayConfiguration = $configuration[0]['array'];
		} catch (Exception $e) {
		    // validation error
		    echo $e->getMessage() . PHP_EOL;
		}

		$this->cache[$scope] = $processedConfiguration;
		$this->arrayCache[$scope] = $arrayConfiguration;

		return $this->cache[$scope];
	}

	public function getConfig($key, $scope) {
		if (!array_key_exists($scope, $this->cache)) {
			$this->loadYaml($scope);
		}

		return $this->cache[$scope][$key];
	}

	public function get($key, $scope) {
		if (!array_key_exists($scope, $this->cache)) {
			$this->loadGroup($scope);
		}

		return $this->cache[$scope][$key];
	}

	public function getArray($scope) {
		if (!array_key_exists($scope, $this->arrayCache)) {
			$this->loadGroup($scope);
		}

		return $this->arrayCache[$scope];
	}
}