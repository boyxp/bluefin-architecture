<?php
declare(strict_types=1);
namespace bluefin\architecture\cache\adapter;
use bluefin\orm\connection\adapter\redis as connection;
use bluefin\architecture\cache\cache as cacheInterface;
class redis implements cacheInterface
{
	private $_redis  = null;
	private $_prefix = null;
	private $_factor = null;

	public function __construct(connection $redis, string $prefix=null)
	{
		$this->_redis = $redis;
		if(is_null($prefix)) {
			$this->_prefix = "CACHE:{$_SERVER['SERVER_NAME']}:";
		} else {
			$this->_prefix = "{$prefix}:";
		}
	}

	public function damping(float $factor):bool
	{
		$this->_factor = abs($factor) > 1 ? 1 : abs($factor);
		return true;
	}

	public function get(string $key)
	{
		return $this->_redis->get($this->_prefix.$key);
	}

	public function __get(string $key)
	{
		return $this->get($this->_prefix.$key);
	}

	public function set(string $key, $value, int $ttl=0):bool
	{
		$result = $this->_redis->set($this->_prefix.$key, $value);
		if($ttl>0) {
			if(!is_null($this->_factor)) {
				$min = intval($ttl-$this->_factor*$ttl);
				$max = intval($ttl+$this->_factor*$ttl);
				$ttl = rand($min, $max);
			}
			$this->expire($this->_prefix.$key, $ttl);
		}

		return $result==='OK';
	}

	public function __set(string $key, $value):bool
	{
		return $this->set($this->_prefix.$key, $value);
	}

	public function exists(string $key):bool
	{
		$result = $this->_redis->exists($this->_prefix.$key);
		return $result===1;
	}

	public function __isset(string $key):bool
	{
		return $this->exists($key);
	}

	public function remove(string $key):bool
	{
		$result = $this->_redis->del($this->_prefix.$key);
		return $result===1;
	}

	public function __unset(string $key):bool
	{
		return $this->delete($key);
	}

	public function expire(string $key, int $ttl=60):bool
	{
		$result = $this->_redis->expire($this->_prefix.$key, $ttl);
		return $result===1;
	}

	public function ttl(string $key):int
	{
		$ttl = $this->_redis->ttl($this->_prefix.$key);
		return $ttl>0 ? $ttl : 0;
	}

	public function flush():bool
	{
	}
}
