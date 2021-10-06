<?php

namespace MyApp\Helper;

use MyApp\Helper\Iterator;

/**
 * Iterator を配下に持つハッシュ
 * @package  App\Helper
 */
class IteratorHash implements \IteratorAggregate, \Countable
{
	/**
	 * 管理するハッシュ
	 * @var array<string, Iterator>
	 */
	protected $source = [];

	public function getIterator()
	{
		return new \ArrayIterator($this->source);
	}

	public function array()
	{
		return $this->source;
	}

	public function count(): int
	{
		return count($this->source);
	}

	public function push(string $key, $obj)
	{
		if (! isset($this->source[$key])) {
			$this->source[$key] = new Iterator();
		}
		$this->source[$key]->push($obj);
	}

	/**
	 * ゲッター
	 *
	 * @param  string $key                  ハッシュのキー
	 * @return \MyApp\Helper\Iterator|null  ハッシュの値
	 */
	public function get(string $key): ?Iterator
	{
		return $this->source[$key] ?? null;
	}
}
