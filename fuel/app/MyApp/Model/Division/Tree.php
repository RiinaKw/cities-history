<?php

/**
 * @package  App\Model\Division
 */

namespace MyApp\Model\Division;

use MyApp\Helper\Iterator;
use Model_Division;
use MyApp\PresentationModel\Division\Tree as PModel;

class Tree
{
	/**
	 * 自分自身の自治体オブジェクト
	 * @var Model_Division
	 */
	protected $self = null;

	/**
	 * 配下にある種別ごとの自治体数
	 * @var array<string, int>
	 */
	protected $suffix_arr = [];

	/**
	 * 配下の自治体
	 * @var array<string, Iterator>
	 */
	protected $children = [];

	/**
	 * 参照？？
	 * @var array<string, Tree>
	 */
	protected $ref = [];

	public function __construct(Model_Division $division)
	{
		$this->self = $division;
		$this->suffix_arr = [
			'支庁' => 0,
			'総合振興局' => 0,
			'振興局' => 0,
			'市' => 0,
			'区' => 0,
			'郡' => 0,
			'町' => 0,
			'村' => 0,
		];
		$this->unknown = new Iterator();
	}

	public function suffixes(): array
	{
		return $this->suffix_arr;
	}

	public function pmodel(): PModel
	{
		return new PModel($this);
	}

	public function self(): Model_Division
	{
		return $this->self;
	}

	public function make_tree($divisions): self
	{
		foreach ($divisions as $division) {
			$this->add($division);
		}
		return $this;
	}

	public function get_by_suffix(string $suffix): ?Iterator
	{
		return $this->children[$suffix] ?? null;
	}

	protected function get_subtree_by_division($division): ?self
	{
		$parent_id_path = dirname($division->id_path) . '/';
		return $this->ref[$parent_id_path] ?? null;
	}

	protected function create_subtree(Model_Division $division): self
	{
		$name = $division->get_fullname();
		$suffix = $division->suffix_classification();

		$tree = new self($division);
		if (! isset($this->children[$suffix])) {
			$this->children[$suffix] = new Iterator();
		}
		$this->children[$suffix]->push($tree, $name);
		return $tree;
	}

	protected function add(Model_Division $division, string $name = ''): void
	{
		if ($name === '') {
			$name = $division->path;
		}
		$suffix = $division->suffix;

		if (!isset($this->suffix_arr[$suffix])) {
			$this->suffix_arr[$suffix] = 0;
		}
		++$this->suffix_arr[$suffix];

		$parent_path = dirname($name);
		$suffix = $division->suffix_classification();

		if (strpos($parent_path, '/') === false) {
			if ($tree = $this->get_subtree_by_division($division)) {
				$tree->self = $division;
			} else {
				$tree = new self($division, $division->fullname);
				if (! isset($this->children[$suffix])) {
					$this->children[$suffix] = new Iterator();
				}
				$this->children[$suffix]->push($tree, $division->fullname);
			}
		} else {
			$tree = $this->get_subtree_by_division($division);
			if ($tree === null) {
				$tree = $this->create_subtree($division);
			}
			$tree->add($division, $division->fullname);
		}
		$this->ref[$division->id_path] = $tree;
	}

	public function dump(int $depth = 2): void
	{
		$indent = str_repeat(' ', $depth);
		echo $indent, $this->self->dump(), PHP_EOL;

		$indent = str_repeat(' ', $depth + 2);
		foreach ($this->children as $suffix => $children) {
			echo $indent, $suffix, PHP_EOL;
			$children->dump($depth + 4);
		}
	}
}
// class Model_Division_Tree
