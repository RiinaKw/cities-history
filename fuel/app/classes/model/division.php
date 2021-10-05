<?php

/**
 * @package  App\Model
 */

use MyApp\Table\Division as DivisionTable;
use MyApp\PresentationModel\Division as PModel;
use MyApp\Getter\Division as Getter;
use MyApp\Model\Division\Tree;

class Model_Division extends Model_Base
{
	protected static $_table_name  = 'divisions';
	protected static $_primary_key = ['id'];
	protected static $_created_at  = 'created_at';
	protected static $_updated_at  = 'updated_at';
	protected static $_deleted_at  = 'deleted_at';
	protected static $_mysql_timestamp = true;

	protected static $_has_many = ['event_details'];

	public const RE_SUFFIX =
		'/^(?<place>.+?)'
		. '(?<suffix>都|府|県|支庁|庁|総合振興局|振興局|市|郡|区|町|村|郷|城下|駅|宿|新宿|組|新田|新地)'
		. '(\((?<identifier>.+?)\))?$/';

	public function pmodel(): PModel
	{
		return new PModel($this);
	}

	public function getter(): Getter
	{
		return new Getter($this);
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

	public function parent(): ?self
	{
		$path = dirname($this->id_path) . '/';
		return static::query()->where('id_path', $path)->get_one();
	}

	/**
	 * パス形式の ID を分割し、各 ID ごとにコールバックを実行
	 * @param callable $callback  コールバック関数
	 */
	public function id_chain(callable $callback): void
	{
		$id_arr = explode('/', $this->id_path);
		foreach ($id_arr as $id) {
			$id = (int)$id;
			if ($id) {
				$callback(static::find($id));
			}
		}
	}

	public function belongs(): ?self
	{
		if ($this->belongs_division_id) {
			return static::find($this->belongs_division_id);
		}
		return null;
	}

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

	public static function create2(array $params, Model_Division $parent = null): self
	{
		$fullname = $params['fullname'];
		preg_match(static::RE_SUFFIX, $fullname, $matches);
		if (! $matches) {
			$matches = [
				'place' => $name,
				'suffix' => '',
			];
		}

		$params = array_merge(
			[
				'id_path' => '',
				'name' => $matches['place'],
				'name_kana' => '',
				'suffix' => $matches['suffix'],
				'suffix_kana' => '',
				'search_path' => '',
				'search_path_kana' => '',
				'fullname' => $fullname,
				'path' => '',
			],
			$params
		);


		$division = Model_Division::forge($params);
		$division->save();

		$division->id_path = ($parent ? $parent->id_path : '') . $division->id . '/';
		$division->path = (($parent ? $parent->path . '/' : '') . $division->fullname);
		$division->save();

		return $division;
	}

	/**
 	 * @SuppressWarnings(PHPMD.ExitExpression)
	 */
	public function createDivision($input)
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

		$path = $parent . '/' . $this->fullname;
		$this->id_path = self::make_id_path($path, $this->id);

		$this->fullname         = $this->fullname;
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
