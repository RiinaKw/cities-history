<?php

/**
 * @package  App\Helper
 */

namespace MyApp\Helper;

use MyApp\Helper\Iterator;

class IteratorHash implements \IteratorAggregate
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

	public function push(string $key, $obj)
	{
		if (! isset($this->source[$key])) {
			$this->source[$key] = new Iterator();
		}
		$this->source[$key]->push($obj);
	}

	/**
	 * ゲッター
	 * @param  string $key  ハッシュのキー
	 * @return mixed        ハッシュの値
	 */
	public function get(string $key): ?Iterator
	{
		return $this->source[$key] ?? null;
	}
}
