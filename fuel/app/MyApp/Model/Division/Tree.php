<?php

/**
 * @package  App\Model\Division
 */

namespace MyApp\Model\Division;

use MyApp\Helper\Iterator;
use MyApp\Helper\SuffixHash;
use MyApp\Helper\IteratorHash;
use Model_Division;
use MyApp\Table\Division as DivisionTable;
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
	 * @var SuffixHash
	 */
	protected $suffixes = null;

	/**
	 * 配下の自治体
	 * @var IteratorHash
	 */
	public $children = null;

	/**
	 * id_path に該当するツリーの参照
	 * @var array<string, Tree>
	 */
	protected static $ref = [];

	public function __construct(Model_Division $division)
	{
		//var_dump("constructed tree of '{$division->fullname}'");

		$this->self = $division;
		$this->children = new IteratorHash();
		$this->suffixes = new SuffixHash();
		$this->unknown = new Iterator();

		static::$ref[$division->id_path] = $this;
	}

	public function pmodel(): PModel
	{
		return new PModel($this);
	}

	/**
	 * その日付に存在する自治体のツリーを生成する
	 * @param  Model_Division $division  ツリーの親となる自治体オブジェクト
	 * @param  ?string        $date      日付、指定がなければ過去に存在したすべての自治体をツリーに含める
	 * @return self                      生成されたツリー
	 */
	public static function create(Model_Division $division, ?string $date): self
	{
		$tree = new static($division);

		$divisions = DivisionTable::get_by_parent_division_and_date($division, $date);
		foreach ($divisions as $division) {
			$tree->add($division);
		}
		return $tree;
	}

	/**
	 * 自治体の種別（市町村など）を追加
	 * @param string $type  自治体種別
	 */
	protected function addSuffix(string $type): void
	{
		$this->suffixes->increment($type);
	}

	/**
	 * 自治体種別の一覧を取得
	 * @return array  種別一覧
	 */
	public function suffixes(): array
	{
		return $this->suffixes->array();
	}

	/**
	 * 自分自身の自治体オブジェクトを取得
	 * @return Model_Division  自治体オブジェクト
	 */
	public function self(): Model_Division
	{
		return $this->self;
	}

	/**
	 * 自治体種別からツリー内の自治体一覧を取得
	 * @param  string  $suffix                 自治体種別
	 * @return MyApp\Helper\IteratorHash|null  自治体の一覧
	 */
	public function get_by_suffix(string $suffix): ?Iterator
	{
		return $this->children->get($suffix) ?? null;
	}

	protected function push(string $suffix, self $subtree): void
	{
		$this->children->push($suffix, $subtree);
	}

	/**
	 * ツリーを作成し、参照に追加
	 * @param  Model_Division $division  ツリーの元となる自治体オブジェクト
	 * @return self                      生成されたツリー
	 */
	protected function ref(Model_Division $division): self
	{
		$id_path = $division->id_path;
		if (! isset(static::$ref[$id_path])) {
			static::$ref[$id_path] = new static($division);
		}
		return static::$ref[$id_path];
	}

	/**
	 * ツリーに自治体を追加する
	 * @param Model_Division $division  追加する自治体オブジェクト
	 */
	protected function add(Model_Division $division): void
	{

/*
Model_Division::id_chain() を使え
Model_Division のテストを作れ　parent(), id_chain()
env の切り替えってどうするの
Tree::ref もハッシュにするか
てか ref って static で良くね？
*/

		$root_tree = $this;
		$target = $division;

		$division->id_chain(function ($division) use ($target, $root_tree) {
			//var_dump("======== target: [$target->id] '{$target->path}', context: '{$division->fullname}' ========");

			if ($division === $target) {
				//var_dump("    current division, **no op**");
				return;
			}

			if ($root_tree->self()->parent() === $division) {
				//var_dump("    '{$division->fullname}' is not under '{$root_tree->self()->fullname}', **no op**");
				return;
			}

			//var_dump("==== create '{$division->path}' ?");
			$parent_tree = $root_tree->ref($division);

			//var_dump("        todo: increment suffix '{$target->path}' in '{$parent_tree->self()->path}'");
			$parent_tree->addSuffix($target->suffix);

			if ($target->parent() !== $division) {
				//var_dump("    '{$division->fullname}' is not under '{$parent->fullname}', **no op**");
				return;
			}

			//var_dump("            todo: {$target->path} into '{$parent_tree->self()->path}'");


			$suffix = $target->pmodel()->suffix_classification();

			$tree = $root_tree->ref($target);
			$parent_tree->children->push($suffix, $tree);
			//var_dump("            success : '{$target->path}' into '{$parent_tree->self()->path}'");
		});
	}
	// function add()

	/**
	 * ダンプ出力
	 * @param integer $depth  インデントの深さ
	 */
	public function dump(int $depth = 2): void
	{
		$indent = str_repeat(' ', $depth);
		$indent_sub = str_repeat(' ', $depth + 2);

		echo $indent, $this->self->dump(), PHP_EOL;

		foreach ($this->children as $suffix => $children) {
			echo $indent_sub, $suffix, PHP_EOL;
			foreach ($children as $subtree) {
				//var_dump( $subtree->children->array() );
				$subtree->dump($depth + 4);
			}
		}
		/*
		echo $indent, 'ref', PHP_EOL;
		foreach ($this->ref as $id => $ref) {
			echo $indent_sub, $id, ' : ', $ref->self()->fullname, PHP_EOL;
		}
		*/
	}
}
// class Model_Division_Tree
