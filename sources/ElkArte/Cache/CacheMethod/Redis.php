<?php

/**
 * This file contains functions that deal with getting and setting cache values.
 *
 * @package   ElkArte Forum
 * @copyright ElkArte Forum contributors
 * @license   BSD http://opensource.org/licenses/BSD-3-Clause (see accompanying LICENSE.txt file)
 *
 * @version 2.0 dev
 *
 */

namespace ElkArte\Cache\CacheMethod;

/**
 * Redis.
 */
class Redis extends AbstractCacheMethod
{
	/**
	 * {@inheritdoc}
	 */
	protected $title		= 'Redis-based caching';

	/**
	 * Creates a Redis instance representing the connection to the Redis server.
	 *
	 * @var \Redis
	 */
	protected $redisServer;

	/**
	 * If the daemon has valid servers in it pool
	 *
	 * @var bool
	 */
	protected $_is_running = false;

	/**
	 * {@inheritdoc}
	 */
	public function __construct($options)
	{
		if (empty($options['servers']))
		{
			$options['servers'] = '';
		}

		parent::__construct($options);

		if ($this->isAvailable())
		{
			$this->redisServer = new \Redis();
			$this->connect();
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function isAvailable()
	{
		return class_exists('\\Redis');
	}

	/**
	 * {@inheritdoc}
	 */
	public function exists($key) {

	}

	/**
	 * {@inheritdoc}
	 */
	public function put($key, $value, $ttl = 120)
	{
		$result = false;

		if(!$this->_is_running) 
			return $result;


		if($this->redisServer instanceof \Redis)
		{
			$this->redisServer->setEx($key, $ttl, $value);
		}

		return $result;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get($key, $ttl = 120)
	{
		if(!$this->_is_running) 
			return '';

		$value = '';

		if($this->redisServer instanceof \Redis)
		{
			$value = $this->redisServer->get($key);
		}
	 
		return $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function clean($type = '')
	{
		$result = false;
		if(!$this->_is_running) 
			return $result;


		if($this->redisServer instanceof \Redis)
		{
			$result = $this->redisServer->flushDb();
		}

		return $result;
	}

	/**
	 * {@inheritdoc}
	 */
	public function details()
	{
		return array('title' => $this->title, 'version' => '1.0.0');
	}

	/**
	 * Adds the settings to the settings page.
	 *
	 * Used by integrate_modify_redis_settings added in the title method
	 *
	 * @param array $config_vars
	 */
	public function settings(&$config_vars)
	{
		global $txt, $modSettings;

		// Should this really be here?
		$modSettings['cache_redis'] = $this->_options['servers'];

		$config_vars[] = array ('cache_redis', $txt['cache_redis'], 'string', 'text', 15, 'redis_ip', 'force_div_id' => 'redis_cache');

	}

	private function connect()
	{
		if(!class_exists('Redis'))
		{
			return false;
		}

		if(!($this->redisServer instanceof \Redis))
		{
			return false;
		}

		list($redis_ip, $redis_port) = explode(':', $this->_options['servers']);
		
		if(empty($redis_ip))
		{
			return false;
		}

		if(empty($redis_port))
		{
			return false;
		}

		$this->_is_running = $this->redisServer->connect($redis_ip, $redis_port);

		if(!empty($redis_user) && !empty($redis_password)) {
			$this->redisServer->auth($redis_user, $redis_password);
		}
		else if(!empty($redis_password)) {
			$this->redisServer->auth($redis_password);
		}

		$this->redisServer->select(0);

		return $this->_is_running;
	}

}
