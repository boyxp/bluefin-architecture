<?php
declare(strict_types=1);
namespace bluefin\architecture\queue\adapter;
use bluefin\orm\connection\adapter\redis as connection;
use bluefin\architecture\queue\queue as queueInterface;
class redis implements queueInterface
{
	private $_redis = null;
	private $_key   = null;

	public function __construct(connection $redis, string $key)
	{
		$this->_redis = $redis;
		$this->_key   = "QUEUE:{$key}";
	}

	public function enqueue(string $message):bool
	{
		$this->_redis->lpush($this->_key, $message);
		return true;
	}

	public function dequeue():string
	{
		$result = $this->_redis->rpop($this->_key);
		return is_null($result) ? '' : $result;
	}

	public function purge():bool
	{
		$result = $this->_redis->del($this->_key);
		return $result===1;
	}

	public function delete():bool
	{
		$result = $this->_redis->del($this->_key);
		return $result===1;
	}
}
