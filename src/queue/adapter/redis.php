<?php
declare(strict_types=1);
namespace bluefin\architecture\queue\adapter;
use bluefin\architecture\queue\queue as queueInterface;
class redis extends \injector implements queueInterface
{
	private $_redis = null;
	private $_key   = null;

	public function __construct(string $key='QUEUE', string $connection='connection_redis')
	{
		if(static::$locator->has($connection)===false) {
			throw new \InvalidArgumentException('error');
		}

		$this->_redis = static::$locator->$connection;
		$this->_key   = $key;
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
