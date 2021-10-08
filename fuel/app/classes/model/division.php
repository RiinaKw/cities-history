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

	public function dump(): void
	{
		echo $this->id_path, ' ', $this->path;
	}
}
// class Model_Division
