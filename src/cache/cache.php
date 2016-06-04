<?php
declare(strict_types=1);
namespace bluefin\architecture\cache;
interface cache
{
	public function damping(float $factor):bool;

	public function get(string $key);
	public function __get(string $key);

	public function set(string $key, $value, int $ttl=0):bool;
	public function __set(string $key, $value):bool;

	public function exists(string $key):bool;
	public function __isset(string $key):bool;

	public function remove(string $key):bool;
	public function __unset(string $key):bool;

	public function expire(string $key, int $ttl):bool;
	public function ttl(string $key):int;

	public function flush():bool;
}
