<?php

/**
 * @package  App\Model
 */

use MyApp\Table\Division as Table;
use MyApp\Model\Division\Tree;

class Model_Division extends \MyApp\Abstracts\ActiveRecord
{
	use MyApp\Traits\Model\Presentable;
	use MyApp\Traits\Model\Gettable;

	protected static $_table_name  = Table::TABLE_NAME;
	protected static $_primary_key = Table::TABLE_PK;

	protected static $_has_many = ['event_details'];

	protected static $_belongs_to = [
		'belongs' => [
			'key_from' => 'belongs_division_id',
			'model_to' => 'Model_Division',
			'key_to' => 'id',
			'cascade_save' => false,
			'cascade_delete' => false,
		]
	];

	public const RE_SUFFIX =
		'/^(?<place>.+?)'
		. '(?<suffix>都|府|県|支庁|庁|総合振興局|振興局|市|郡|区|町|村|郷|城下|駅|宿|新宿|組|新田|新地)'
		. '(\((?<identifier>.+?)\))?$/';

	/**
	 * プレゼンテーションモデルのクラス名
	 * @var string
	 */
	protected static $pmodel_class = \MyApp\PresentationModel\Division::class;

	/**
	 * ゲッターのクラス名
	 * @var string
	 */
	protected static $getter_class = \MyApp\Getter\Division::class;

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
/*
	public function belongs(): ?self
	{
		if ($this->belongs_division_id) {
			return static::find($this->belongs_division_id);
		}
		return null;
	}
*/
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
	 * 自治体の基本情報を設定
	 * @param  array               $params    フォームからの入力
	 * @param  Model_Division|null $division  編集するオブジェクト、null の場合は新規作成
	 * @return self                           情報が設定されたオブジェクト（未保存であることに注意）
	 */
	public static function make(array $params, self $division = null): self
	{
		if (! $division) {
			$division = new static();
		}

		$name = $params['name'] ?? null;
		$suffix = $params['suffix'] ?? null;
		if (! $name || ! $suffix) {
			$fullname = $params['fullname'];
			preg_match(static::RE_SUFFIX, $fullname, $matches);
			if (! $matches) {
				$matches = [
					'place' => $fullname,
					'suffix' => '',
				];
			}
			$name = $matches['place'];
			$suffix = $matches['suffix'];
		} else {
			$fullname = $name . $suffix;
		}
		$fullname .= $params['identifier'] ?? '';

		$params = array_merge(
			[
				'name' => $name,
				'name_kana' => '',
				'suffix' => $suffix,
				'suffix_kana' => '',
				'search_path' => '',
				'search_path_kana' => '',
				'fullname' => $fullname,
				'path' => '',
				'government_code' => '',
				'display_order' => '',
				'source' => '',
			],
			$params
		);

		// 基本情報を設定
		$division->name            = $params['name'];
		$division->suffix          = $params['suffix'];
		$division->identifier      = $params['identifier'] ?? '';
		$division->fullname        = $fullname;
		$division->name_kana       = $params['name_kana'];
		$division->suffix_kana     = $params['suffix_kana'];
		$division->government_code = $params['government_code'];
		$division->show_suffix     = isset($params['show_suffix']);
		$division->is_unfinished   = isset($params['is_unfinished']);
		$division->display_order   = $params['display_order'] ?: null;
		$division->source          = $params['source'] ?: null;

		$division->is_empty_government_code = ($params['government_code'] === '');
		$division->is_empty_kana = ($params['name_kana'] === '');

		// パスなど、親自治体が決定しないと設定できない項目は仮のデータを入れておく
		$division->path = '';
		$division->id_path = '';
		$division->search_path = '';
		$division->search_path_kana = '';

		return $division;
	}

	/**
	 * 親自治体を元にパスなどの項目を設定
	 * @param  Model_Division|null $parent  親自治体オブジェクト、なければ null
	 * @return self                         自分自身
	 */
	public function makePath(self $parent = null): self
	{
		$name = $this->name . $this->suffix;
		$kana = $this->name_kana . $this->suffix_kana;

		$this->id_path = ($parent ? $parent->id_path : '') . $this->id . '/';
		$this->path = (($parent ? $parent->path . '/' : '') . $this->fullname);
		$this->search_path = (($parent ? $parent->search_path : '') . $name);
		$this->search_path_kana = (($parent ? $parent->search_path_kana : '') . $kana);

		return $this;
	}

	/**
	 * 自分自身を親とする自治体を一括更新
	 * @return self  自分自身
	 */
	public function updateChild(): self
	{
		$children = static::query()
			->where('id_path', 'LIKE', $this->id_path . '%_')
			->get();
		foreach ($children as $child) {
			$child->makePath($this);
			$child->save();
		}
		return $this;
	}

	/**
	 * 入力パラメータと親自治体から新規に自治体オブジェクトを登録する
	 * @param  array<string, string>  $params  フォームからの入力
	 * @param  Model_Division|null  $parent    親自治体、なければ null
	 * @return self                            DB に保存された自治体オブジェクト
	 * @todo メソッド名をなんとかしろ
	 */
	public static function create2(array $params, self $parent = null): self
	{
		$division = static::make($params);
		$division->save();

		$division->makePath($parent);
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
			$belongs_division = Table::findByPath($belongs);
			if (! $belongs_division) {
				$belongs_division = Table::set_path($belongs);
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
