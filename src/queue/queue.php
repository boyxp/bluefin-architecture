<?php
declare(strict_types=1);
namespace bluefin\architecture\queue;
interface queue
{
	public function enqueue(string $message):bool;
	public function dequeue():string;
	public function purge():bool;
	public function delete():bool;
}
