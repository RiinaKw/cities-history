<?php

/**
 * @package  App\Table
 */

namespace MyApp\Table;

use Fuel\Core\Database_Result_Cached as Result;
use DB;
use Model_Division;

/**
 * 自治体テーブルを管理するクラス
 */
class Division extends \MyApp\Abstracts\Table
{
	public const TABLE_NAME = 'divisions';
	public const TABLE_PK = ['id'];

	/**
	 * 自治体の種別を判定する正規表現
	 * @var string
	 */
	public const RE_SUFFIX =
		'/^(?<place>.+?)'
		. '(?<suffix>都|府|県|支庁|庁|総合振興局|振興局|市|郡|区|町|村|郷|城下|駅|宿|新宿|組|新田|新地)'
		. '(\((?<identifier>.+?)\))?$/';

	/**
	 * パス形式から自治体を検索
	 * @param  string $path         指定したパス
	 * @return Model_Division|null  自治体オブジェクト、見つからなかった場合は null
	 */
	public static function findByPath(string $path): ?Model_Division
	{
		return Model_Division::query()->where('path', $path)->get_one();
	}
	// function get_by_path()

	/**
	 * 削除されていないすべての自治体一覧を取得
	 * @param  string $q                          検索キーワード
	 * @return \Fuel\Core\Database_Result_Cached  Fuel のデータベースキャッシュ
	 *
	 * @todo 使いみちがよく分からない、Controller_Rest_Division で使っているようだが……
	 */
	public static function query(string $q): Result
	{
		$query = DB::select()
			->from(self::TABLE_NAME)
			->where('deleted_at', '=', null)
			//->where(DB::expr('MATCH(fullname)'), 'AGAINST', DB::expr('(\'+'.$q.'\' IN BOOLEAN MODE)'));
			->where('fullname', 'LIKE', '%' . $q . '%');

		return $query->as_object(Model_Division::class)->execute();
	}
	// function query()

	/**
	 * 自治体検索
	 *
	 * スラッシュ区切りでないフルパス（例：群馬県佐波郡伊勢崎町）と読みがなを検索対象とする
	 *
	 * @param  string $q                          検索キーワード
	 * @return \Fuel\Core\Database_Result_Cached  Fuel のデータベースキャッシュ
	 */
	public static function search(string $q): Result
	{
		$q = str_replace(array('\\', '%', '_'), array('\\\\', '\%', '\_'), $q);
		$q_arr = preg_split('/(\s+)|(　+)/', $q);
		$query = DB::select()
			->from(self::TABLE_NAME)
			->where('deleted_at', '=', null);
		foreach ($q_arr as $word) {
			$query->and_where_open()
				->where('search_path', 'LIKE', '%' . $word . '%')
				->or_where('search_path_kana', 'LIKE', '%' . $word . '%')
				->and_where_close();
		}
		$query
			->order_by('is_empty_government_code', 'asc')
			->order_by('government_code', 'asc')
			->order_by('is_empty_kana', 'asc')
			->order_by('name_kana', 'asc')
			->order_by('end_date', 'desc');

		return $query->as_object(Model_Division::class)->execute();
	}
	// function search()

	/**
	 * 自治体の基本情報を設定
	 * @param  array               $params    フォームからの入力
	 * @param  Model_Division|null $division  編集するオブジェクト、null の場合は新規作成
	 * @return Model_Division                 情報が設定されたオブジェクト
	 */
	protected static function make(array $params, Model_Division $division = null): Model_Division
	{
		if (! $division) {
			$division = new Model_Division();
		}

		$fullname = $params['fullname'] ?? '';
		if (! $fullname) {
			$fullname = ($params['name'] ?? '') . ($suffix = $params['suffix'] ?? '');
		}
		preg_match(static::RE_SUFFIX, $fullname, $matches);
		if (! $matches) {
			$matches = [
				'place' => $fullname,
				'suffix' => '',
			];
			$params['show_suffix'] = false;
		}
		$name = $matches['place'];
		$suffix = $matches['suffix'];
		$fullname = $name . $suffix . ($params['identifier'] ?? '');

		$params = array_merge(
			[
				'name_kana' => '',
				'suffix_kana' => '',
				'search_path' => '',
				'search_path_kana' => '',
				'path' => '',
				'government_code' => '',
				'display_order' => '',
				'source' => '',
			],
			$params
		);

		// 基本情報を設定
		$division->name            = $name;
		$division->suffix          = $suffix;
		$division->identifier      = $params['identifier'] ?? '';
		$division->fullname        = $fullname;
		$division->name_kana       = $params['name_kana'];
		$division->suffix_kana     = $params['suffix_kana'];
		$division->government_code = $params['government_code'];
		$division->show_suffix     = isset($params['show_suffix']);
		$division->is_unfinished   = isset($params['is_unfinished']);
		$division->display_order   = $params['display_order'] ?: null;
		$division->source          = $params['source'] ?: null;

		$division->show_suffix = (isset($params['show_suffix']) ? (bool)$params['show_suffix'] : false);
		$division->is_empty_government_code = ($params['government_code'] === '');
		$division->is_empty_kana = ($params['name_kana'] === '');

		// パスなど、親自治体が決定しないと設定できない項目は仮のデータを入れておく
		$division->path = '';
		$division->id_path = '';
		$division->search_path = '';
		$division->search_path_kana = '';
		$division->save();

		return $division;
	}

	/**
	 * 親自治体を元にパスなどの項目を設定
	 * @param  Model_Division      $division  対象の自治体オブジェクト
	 * @param  Model_Division|null $parent    親自治体オブジェクト、なければ null
	 */
	protected static function makePath(Model_Division $division, Model_Division $parent = null): void
	{
		$name = $division->name . $division->suffix;
		$kana = $division->name_kana . $division->suffix_kana;

		$division->id_path = ($parent ? $parent->id_path : '') . $division->id . '/';
		$division->path = (($parent ? $parent->path . '/' : '') . $division->fullname);
		$division->search_path = (($parent ? $parent->search_path : '') . $name);
		$division->search_path_kana = (($parent ? $parent->search_path_kana : '') . $kana);

		static::requireUnique($division->path, $division->id);
		$division->save();
	}

	/**
	 * 入力パラメータと親自治体から新規に自治体オブジェクトを登録する
	 * @param  array<string, string>  $params  フォームからの入力
	 * @param  Model_Division|null    $parent  親自治体、なければ null
	 * @return Model_Division                  作成された自治体オブジェクト
	 */
	public static function create(array $params, Model_Division $parent = null): Model_Division
	{
		$division = static::make($params);
		static::makePath($division, $parent);
		return $division;
	}

	/**
	 * 入力パラメータと親自治体から自治体オブジェクトを更新する
	 * @param  Model_Division         $division  更新対象の自治体オブジェクト
	 * @param  array<string, string>  $params    フォームからの入力
	 * @param  Model_Division|null    $parent    親自治体、なければ null
	 * @return Model_Division                    更新された自治体オブジェクト
	 */
	public static function update(
		Model_Division $division,
		array $params,
		Model_Division $parent = null
	): Model_Division {
		$division = static::make($params, $division);
		static::makePath($division, $parent);
		static::updateChild($division);
		return $division;
	}

	/**
	 * 自治体のパスが一意であることを保証する
	 * @param string   $path       対象のパス
	 * @param int|null $ignore_id  除外する自治体 ID
	 * @throws \Exception  すでにパスが存在する場合
	 */
	protected static function requireUnique(string $path, int $ignore_id = null): void
	{
		$division = static::findByPath($path);
		if ($division && $division->id !== $ignore_id) {
			throw new \Exception("重複しています : '{$path}'");
		}
	}

	/**
	 * 指定した自治体に所属する自治体を一括更新
	 * @param  Model_Division $division  親となる自治体
	 */
	protected static function updateChild(Model_Division $division): void
	{
		$children = Model_Division::query()
			->where('id_path', 'LIKE', $division->id_path . '%_')
			->get();
		foreach ($children as $child) {
			static::makePath($child, $division);
		}
	}

	/**
	 * パス形式から自治体オブジェクトを生成する
	 * @param  string         $path  パス
	 * @return Model_Division        生成された自治体オブジェクト
	 */
	public static function makeFromPath(string $path): Model_Division
	{
		static::requireUnique($path);
		$name = basename($path);
		$parent_path = dirname($path);

		// 親を取得
		$parent = static::getOrCreateFromPath($parent_path);

		$division = static::make([
			'fullname' => $name,
			'show_suffix' => true,
		]);
		static::makePath($division, $parent);
		$division->save();
		return $division;
	}

	/**
	 * パスから自治体オブジェクトを取得するか、なければ作る
	 * @param  string $path    パス
	 * @return Model_Division  自治体オブジェクト
	 */
	public static function getOrCreateFromPath(string $path): Model_Division
	{
		$division = static::findByPath($path);
		if (! $division) {
			$division = static::makeFromPath($path);
		}
		return $division;
	}

	/**
	 * どの自治体にも属していない自治体（だいたい都道府県）の一覧を取得
	 *
	 * @return \Fuel\Core\Database_Result_Cached  Fuel のデータベースキャッシュ
	 */
	public static function get_top_level(): Result
	{
		$query = DB::select()
			->from(self::TABLE_NAME)
			->where('deleted_at', '=', null)
			->where('id_path', '=', DB::expr('CONCAT(id, "/")'))
			->order_by('display_order', 'asc');

		return $query->as_object(Model_Division::class)->execute();
	}
	// function get_top_level()

	/**
	 * 親自治体と日付から、その日に存在した自治体一覧を取得
	 * @param  Model_Division $parent  親自治体
	 * @param  string|null    $date    日付、null の場合は過去に存在したすべての所属自治体を取得
	 * @return [type]                 [description]
	 */
	public static function get_by_parent_division_and_date(Model_Division $parent, string $date = null)
	{
		$query = DB::select('d.*')
			->from([self::TABLE_NAME, 'd'])
			->join(['events', 's'], 'LEFT OUTER')
			->on('d.start_event_id', '=', 's.id')
			->join(['events', 'e'], 'LEFT OUTER')
			->on('d.end_event_id', '=', 'e.id')
			->and_where_open()
			->where('d.id_path', 'LIKE', DB::expr('CONCAT("' . $parent->id_path . '", "_%")'))
			->or_where('d.belongs_division_id', '=', $parent->id)
			->and_where_close()
			->where('d.deleted_at', '=', null);
		if ($date) {
			$query->and_where_open()
				->where('s.date', '<=', $date)
				->or_where('s.date', '=', null)
				->and_where_close()
				->and_where_open()
				->where('e.date', '>', $date)
				->or_where('e.date', '=', null)
				->and_where_close();
		}
		$query
			->order_by(DB::expr('LENGTH(d.id_path)'), 'asc')
			->order_by('d.is_empty_government_code', 'asc')
			->order_by('d.government_code', 'asc')
			->order_by('d.is_empty_kana', 'asc')
			->order_by('d.name_kana', 'asc')
			->order_by('d.end_date', 'desc');

		return $query->as_object(Model_Division::class)->execute();
	}
	// function get_by_parent_division_and_date()

	/**
	 * 情報不足の自治体一覧を取得
	 */
	public static function get_by_admin_filter($parent, $filter)
	{
		$query = DB::select()
			->from([self::TABLE_NAME, 'd'])
			->where('d.deleted_at', null);
		if ($parent) {
			$query->where('d.id_path', 'LIKE', DB::expr('CONCAT("' . $parent->id_path . '", "_%")'));
		} else {
			$query->where('id_path', '=', DB::expr('CONCAT(d.id, "/")'));
		}

		switch ($filter) {
			case 'empty_kana':
				$query->where('d.is_empty_kana', 1);
				break;

			case 'empty_code':
				$query
					->join(['events', 's'], 'LEFT OUTER')
					->on('d.start_event_id', '=', 's.id')

					->where('d.suffix', '!=', '郡')

					->and_where_open()
					->where('s.date', '>=', '1970-04-01')
					->and_where_close()

					->and_where_open()
					->where('d.government_code', null)
					->or_where('d.government_code', '')
					->and_where_close();
				break;

			case 'empty_source':
				$query
					->and_where_open()
					->where('d.source', null)
					->or_where('d.source', '')
					->and_where_close();
				break;

			case 'is_wikipedia':
				$query->where(DB::expr('LOWER(d.source)'), 'LIKE', '%wikipedia%');
				break;
		}

		$query
			->order_by('d.display_order', 'asc')
			->order_by('d.is_empty_government_code', 'asc')
			->order_by('d.government_code', 'asc')
			->order_by('d.is_empty_kana', 'asc')
			->order_by('d.name_kana', 'asc')
			->order_by('d.end_date', 'desc');

		return $query->as_object(Model_Division::class)->execute();
	}
	// functiob get_by_admin_filter()
}
