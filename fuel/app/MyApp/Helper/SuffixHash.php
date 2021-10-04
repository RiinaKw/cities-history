<?php

/**
 * @package  App\Helper
 */

namespace MyApp\Helper;

class SuffixHash implements \IteratorAggregate
{
	/**
	 * 管理するハッシュ
	 * @var array<string, int>
	 */
	protected $source = [];

	public function getIterator() {
		return new \ArrayIterator($this->source);
	}

	public function increment(string $key)
	{
		if (! isset($this->source[$key])) {
			$this->source[$key] = 0;
		}
		$this->source[$key]++;
	}

	/**
	 * ゲッター
	 * @param  string $key  ハッシュのキー
	 * @return mixed        ハッシュの値
	 */
	public function get(string $key): int
	{
		return $this->source[$key] ?? 0;
	}

	public function array(): array
	{
		return $this->source;
	}
}
