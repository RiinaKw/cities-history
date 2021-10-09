<?php

namespace MyApp\Helper;

use Model_Division;
use MyApp\Helper\Uri;

/**
 * パンくずリストを生成するヘルパークラス
 *
 * @package  App\Helper
 */
class Breadcrumb implements \IteratorAggregate, \Countable
{
	protected $array;

	public function getIterator()
	{
		return new \ArrayIterator($this->array);
	}

	public function count(): int
	{
		return count($this->array);
	}

	protected function __construct(string $label = null, string $uri = null)
	{
		$this->array = [
			'Top' => Uri::create('top'),
		];
		if ($label) {
			$this->push($label, $uri);
		}
	}

	public static function forge(string $label = null, string $uri = null): self
	{
		return new static($label, $uri);
	}

	/**
	 * パンくずの末尾に項目を追加
	 * @param  string      $label  項目の表示名
	 * @param  string|null $url    項目の URI、リンクを張らない場合は null
	 * @return self
	 */
	public function push(string $label, string $uri = null): self
	{
		$this->array[$label] = $uri;
		return $this;
	}

	public function division(
		Model_Division $division
	): self {
		if ($division) {
			$ids = explode('/', substr($division->id_path, 0, -1));
			foreach ($ids as $id) {
				$parent = Model_Division::find($id);
				if (! $parent) {
					break;
				}
				$this->push($parent->fullname, $parent->pmodel()->uri('timeline'));
			}
		}
		return $this;
	}
	// function division()
}
