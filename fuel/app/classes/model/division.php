<?php

use MyApp\Table\Division as DivisionTable;
use MyApp\PresentationModel\Division as PModel;
use MyApp\Model\Division\Tree;

/**
 * @package  App\Model
 */
class Model_Division extends Model_Base
{
	protected static $_table_name  = 'divisions';
	protected static $_primary_key = 'id';
	protected static $_created_at  = 'created_at';
	protected static $_updated_at  = 'updated_at';
	protected static $_deleted_at  = 'deleted_at';
	protected static $_mysql_timestamp = true;

	public function pmodel(): PModel
	{
		return new PModel($this);
	}

	public function validation()
	{
		$validation = Validation::forge(mt_rand());

		// rules
		$validation->add('name', '自治体名')
			->add_rule('required')
			->add_rule('max_length', 20);
		$validation->add('name_kana', '自治体名かな')
			->add_rule('required')
			->add_rule('max_length', 20);
		$validation->add('suffix', '自治体名種別')
			->add_rule('required')
			->add_rule('max_length', 20);
		$validation->add('suffix_kana', '自治体名種別かな')
			->add_rule('required')
			->add_rule('max_length', 20);
		$validation->add('identifier', '識別名')
			->add_rule('max_length', 50);
		$validation->add('start_event_id', '設置イベント');
		$validation->add('end_event_id', '廃止イベント');
		$validation->add('government_code', '全国地方公共団体コード')
			->add_rule('min_length', 6)
			->add_rule('max_length', 7);
		$validation->add('display_order', '表示順')
			->add_rule('valid_string', array('numeric'));
		$validation->add('source', '出典');

		return $validation;
	}
	// function validation()

	public function get_tree($date): Tree
	{
		$divisions = DivisionTable::get_by_parent_division_and_date($this, $date);
		$tree = new Tree($this);
		return $tree->make_tree($divisions);
	}

	/**
	 * パス形式の ID を分割し、各 ID ごとにコールバックを実行
	 * @param callable $callback  コールバック関数
	 */
	protected function id_chain(callable $callback): void
	{
		$id_arr = explode('/', $this->id_path);
		foreach ($id_arr as $id) {
			$id = (int)$id;
			if ($id) {
				$callback(self::find_by_pk($id));
			}
		}
	}

	public function get_path(): string
	{
		if ($this->path) {
			return $this->path;
		} else {
			return $this->make_path();
		}
	}
	// function get_path()

	/**
	 * パスを生成
	 *
	 * 例 : 群馬県/甘楽郡(1950-)/下仁田町(1955-)
	 *
	 * @return string
	 */
	public function make_path(): string
	{
		$name_arr = [];
		$this->id_chain(function ($d) use (&$name_arr) {
			$name_arr[] = $d->get_fullname();
		});
		return implode('/', $name_arr);
	}
	// function make_path()

	/**
	 * よみがなのパスを生成
	 *
	 * 例 : ぐんま・けん/かんら・ぐん/しもにた・まち
	 *
	 * @return string
	 */
	public function make_path_kana(): string
	{
		$kana_arr = [];
		$this->id_chain(function ($d) use (&$kana_arr) {
			$kana_arr[] = $d->fullname_kana;
		});
		return implode('/', $kana_arr);
	}
	// function make_path_kana()

	/**
	 * 検索用のパスを生成
	 *
	 * 例 : 群馬県甘楽郡下仁田町
	 *
	 * @return string
	 */
	public function make_search_path(): string
	{
		$name_arr = [];
		$this->id_chain(function ($d) use (&$name_arr) {
			$name_arr[] = $d->search_fullname;
		});
		return implode('', $name_arr);
	}
	// function make_path()

	/**
	 * 検索用のよみがなのパスを生成
	 *
	 * 例 : ぐんまけんかんらぐんしもにたまち
	 *
	 * @return string
	 */
	public function make_search_path_kana(): string
	{
		$kana_arr = [];
		$this->id_chain(function ($d) use (&$kana_arr) {
			$kana_arr[] = $d->search_fullname_kana;
		});
		return implode('', $kana_arr);
	}
	// function make_path_kana()

	/**
	 * @todo DB に「fullname」って必要なくない？ マジックメソッド配下にしたいんだけど
	 */
	public function __get($key)
	{
		switch ($key) {
			default:
				return parent::__get($key);

			case 'fullname_kana':
				return $this->name_kana . ($this->show_suffix ? '・' . $this->suffix_kana : '');

			case 'search_fullname':
				return $this->name . ($this->show_suffix ? $this->suffix : '');

			case 'search_fullname_kana':
				return $this->name_kana . ($this->show_suffix ? $this->suffix_kana : '');

			case 'parent_path':
				$path = $this->get_path();
				if (strpos($path, '/') !== false) {
					return dirname($path);
				}
				return null;
		}
	}

	public function get_belongs_path(): ?string
	{
		if ($this->belongs_division_id) {
			$division = self::find_by_pk($this->belongs_division_id);
			return $division->get_path();
		} else {
			return null;
		}
	}
	// function get_belongs_path()

	public function get_belongs_name(): ?string
	{
		if ($this->belongs_division_id) {
			$division = self::find_by_pk($this->belongs_division_id);
			return $division->get_fullname();
		} else {
			return null;
		}
	}
	// function get_belongs_name()

	public function suffix_classification(): string
	{
		switch ($this->suffix) {
			default:
				return $this->suffix;

			case '町':
			case '村':
				return '町村';

			case '支庁':
			case '振興局':
			case '総合振興局':
				return '支庁';
		}
	}

	public function get_fullname(): string
	{
		$name = $this->name;
		if ($this->show_suffix) {
			$name .= $this->suffix;
		}
		if ($this->identifier) {
			$name .= "({$this->identifier})";
		}
		return $name;
	}
	// function get_fullname()

	/**
	 * 必要なパラメータが設定されている場合のみコールバックを実行
	 *
	 * @param array&<string, mixed>   $array     対象の配列
	 * @param string                  $key       対象のキー
	 * @param callable                $callback  実行するコールバック
	 */
	protected function callIfNotEmpty(array &$array, string $key, callable $callback): void
	{
		if (isset($array[$key])) {
			$callback($this, $array[$key]);
		}
	}

	/**
 	 * @SuppressWarnings(PHPMD.ExitExpression)
	 */
	public function create($input)
	{
		$belongs = $input['belongs'] ?? null;
		$parent = $input['parent'] ?? null;

		if ($belongs) {
			$belongs_division = DivisionTable::get_by_path($belongs);
			if (! $belongs_division) {
				$belongs_division = DivisionTable::set_path($belongs);
				$belongs_division = array_pop($belongs_division);
			}
			$this->belongs_division_id = $belongs_division->id;
		} else {
			$this->belongs_division_id = null;
		}

		$this->name = $input['name'] ?? null;

		$this->callIfNotEmpty($input, 'name_kana', function ($obj, $value) {
			$obj->name_kana       = Helper_String::to_hiragana($value);
			$obj->is_empty_kana   = empty($value);
		});

		$this->suffix = $input['suffix'] ?? null;
		$this->suffix_kana = $input['suffix_kana'] ? Helper_String::to_hiragana($input['suffix_kana']) : null;
		$this->show_suffix = $input['show_suffix'] ? (bool)$input['show_suffix'] : false;

		$this->callIfNotEmpty($input, 'government_code', function ($obj, $value) {
			$obj->government_code = Helper_Governmentcode::normalize($value);
			$obj->is_empty_government_code = empty($value);
		});

		$this->display_order = $input['display_order'] ?? null;
		$this->is_unfinished = $input['is_unfinished'] ? (bool)$input['is_unfinished'] : false;
		$this->identifier = $input['identifier'] ?? null;
		$this->source = $input['source'] ?? null;

		$this->search_path = '';
		$this->search_path_kana = '';
		$this->save();

		$path = $parent . '/' . $this->get_fullname();
		$this->id_path = self::make_id_path($path, $this->id);

		$this->fullname         = $this->get_fullname();
		$this->path             = $this->make_path();

		$this->search_path      = $this->make_search_path();
		$this->search_path_kana = $this->make_search_path_kana();

		$query = DB::select()
			->from(self::$_table_name)
			->where('deleted_at', '=', null)
			->where('path', '=', $this->path)
			->where('id', '!=', $this->id)
			;
		if ($query->execute()->count()) {
			throw new HttpBadRequestException('重複しています。');
		}
		$this->save();
	}
	// function create()

	public function dump(): void
	{
		echo $this->id_path, ' ', $this->path;
	}
}
// class Model_Division
