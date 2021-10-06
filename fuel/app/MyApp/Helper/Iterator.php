<?php

namespace MyApp\Helper;

/**
 * 配列のラッパークラス
 * @package  App\Helper
 */
class Iterator implements \IteratorAggregate, \Countable
{
	protected $source = [];

	public function getIterator()
	{
		return new \ArrayIterator($this->source);
	}

	public function array(): array
	{
		return $this->source;
	}

	public function count(): int
	{
		return count($this->source);
	}

	public function get($key)
	{
		return $this->source[$key] ?? null;
	}

	public function push($item, $key = null)
	{
		if ($key === null) {
			$this->source[] = $item;
		} else {
			$this->source[$key] = $item;
		}
	}

	public function dump($depth = 2)
	{
		foreach ($this->source as $item) {
			$item->dump($depth);
		}
	}
}
