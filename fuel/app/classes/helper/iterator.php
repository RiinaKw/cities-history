<?php

class Helper_Iterator implements \iterator
{
	protected $position = 0;
	protected $array = [];

	public function __construct()
	{
		reset($this->array);
	}

	public function current()
	{
		return current($this->array);
	}

	public function next()
	{
		next($this->array);
	}

	public function key()
	{
		return key($this->array);
	}

	public function valid()
	{
		return $this->key() !== null;
	}

	public function rewind()
	{
		reset($this->array);
	}

	public function get($key)
	{
		return $this->array[$key] ?: null;
	}

	public function push($item, $key = null)
	{
		if ($key === null) {
			$this->array[] = $item;
		} else {
			$this->array[$key] = $item;
		}
	}

	public function dump($depth = 2)
	{
		foreach ($this->array as $item) {
			$item->dump($depth);
		}
	}
}
