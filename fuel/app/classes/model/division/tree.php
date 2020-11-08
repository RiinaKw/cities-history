<?php

class Model_Division_Tree
{
	protected $self = null;
	protected $suffix_arr = [];
	protected $children = [];
	protected $ref = [];

	public function __construct($division)
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
		$this->unknown = new \Helper_Iterator;
	}

	public function self()
	{
		return $this->self;
	}

	public function make_tree($divisions)
	{
		foreach ($divisions as $division) {
			$this->add($division);
		}
		return $this;
	}

	public function suffix_count($suffix = null)
	{
		return $suffix ? ($this->suffix_arr[$suffix] ?? null) : $this->suffix_arr;
	}

	public function get_by_suffix($suffix)
	{
		return $this->children[$suffix] ?? [];
	}

	protected function get_subtree_by_division($division)
	{
		$parent_id_path = dirname($division->id_path) . '/';
		return $this->ref[$parent_id_path] ?? null;
	}

	protected function create_subtree($division)
	{
		$name = $division->path;
		$names = explode('/', $name);
		array_shift($names);
		array_pop($names);

		foreach ($names as $name) {
			$suffix = Model_Division::get_suffix($name);
			$tree = new self($name, $name);
			if (! isset($this->children[$suffix])) {
				$this->children[$suffix] = new \Helper_Iterator;
			}
			$this->children[$suffix]->push($tree, $name);
			return $tree;
		}
		return null;
	}

	protected function add($division, $name = '')
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
		$pos = strpos($name, '/');
		if ($pos !== false) {
			$top_parent_path = substr($name, 0, $pos);
			$child_path = substr($name, $pos + 1);
		} else {
			$top_parent_path = $name;
			$child_path = '';
		}

		switch ($suffix)
		{
			case '町':
			case '村':
				$suffix = '町村';
			break;

			case '支庁':
			case '振興局':
			case '総合振興局':
				$suffix = '支庁';
			break;
		}

		if (strpos($parent_path, '/') === false) {
			//$names = explode('/', $name);
			$parent_id_path = dirname($division->id_path) . '/';
			if ($tree = $this->get_subtree_by_division($division)) {
				$tree->self = $division;
			} else {
				$tree = new self($division, $division->fullname);
				if (! isset($this->children[$suffix])) {
					$this->children[$suffix] = new \Helper_Iterator;
				}
				$this->children[$suffix]->push($tree, $division->fullname);
			}
		} else {
			$parent_name = dirname($child_path);
			$tree = $this->get_subtree_by_division($division);
			if ( $tree === null ) {
				$tree = $this->create_subtree($division);
				$tree->add($division, $division->fullname);
			}
			$tree->add($division, $division->fullname);
		}
		$this->ref[$division->id_path] = &$tree;
	}

	public static function dump_division($division, $nest = 0)
	{
		$indent = str_repeat(' ', $nest);
		echo $indent, $division->id_path, ' ', $division->path, PHP_EOL;
	}

	public function dump($depth = 2)
	{
		//var_dump($this->suffix_arr);
		static::dump_division($this->self, $depth);
		foreach ($this->children as $suffix => $children) {
			$children->dump($depth + 2);
		}
	}
} // class Model_Division_Tree
