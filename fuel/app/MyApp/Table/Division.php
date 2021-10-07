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
	 * @todo 意図がよく分からない
	 */
	public static function set_path($path)
	{
		$arr = explode('/', $path);
		$parent = null;
		$divisions = [];
		foreach ($arr as $name) {
			if (! $name) {
				throw new \Exception('自治体名が入力されていません');
			}
			preg_match(static::RE_SUFFIX, $name, $matches);
			if (! $matches) {
				$matches = [
					'place' => $name,
					'suffix' => '',
				];
			}
			if (! $division = self::get_one_by_name_and_parent($matches, $parent)) {
				$division = Model_Division::forge([
					'id_path' => '',
					'name' => $matches['place'],
					'name_kana' => '',
					'suffix' => $matches['suffix'],
					'suffix_kana' => '',
					'fullname' => '',
					'path' => '',
					'show_suffix' => true,
					'identifier' => (isset($matches['identifier']) ? $matches['identifier'] : null),
					'is_unfinished' => true,
					'is_empty_government_code' => true,
					'is_empty_kana' => true,
					'search_path' => '',
					'search_path_kana' => '',
					'end_date' => '9999-12-31',
					'source' => '',
				]);

				$division->save();

				$division->id_path = self::make_id_path($path, $division->id);

				$division->fullname         = $division->get_fullname();
				$division->path             = $division->make_path();

				$division->search_path      = $division->make_search_path();
				$division->search_path_kana = $division->make_search_path_kana();

				$division->save();

				Model_Activity::insert_log([
					'user_id' => Session::get('user_id'),
					'target' => 'add division',
					'target_id' => $division->id,
				]);
			}
			$divisions[] = $division;
			$parent = $division;
		}
		return $divisions;
	}
	// function set_path()

	/**
	 * @todo 意図がよく分からない
	 */
	public static function set_path_as_array($arr)
	{
		foreach ($arr as $item) {
			if (trim($item['path']) === '') {
				continue;
			}
			$divisions = self::set_path($item['path']);

			$division = array_pop($divisions);
			$division->name_kana = $item['name_kana'] ?: null;
			$division->suffix_kana = $item['suffix_kana'] ?: null;
			$division->government_code = $item['code'] ?: null;

			$division->fullname      = $division->get_fullname();
			$division->path          = $division->make_path();

			$division->save();
		}
	}
	// function set_path_as_array()

	/**
	 * @todo 意図がよく分からない
	 */
	public static function make_id_path($path, $self_id)
	{
		$parents = [];
		$cur_path = $path;
		while ($cur_path) {
			$parent = dirname($cur_path);
			if ($parent === '\\' || $parent === '/' || $parent === '.') {
				break;
			}
			$parents[] = $parent;
			if (strpos($parent, '/') === false) {
				break;
			}
			$cur_path = $parent;
		}
		$parents = array_reverse($parents);

		$id_arr = [];
		foreach ($parents as $parent_path) {
			$d = self::get_by_path($parent_path);
			if ($d) {
				$id_arr[] = $d->id;
			}
		}
		$id_arr[] = $self_id;
		return implode('/', $id_arr) . '/';
	}
	// function make_id_path()

	/**
	 * @todo 意図がよく分からない
	 */
	public static function get_by_path($path): ?Model_Division
	{
		$result = Model_Division::query()->where('path', $path)->get();
		return count($result) ? array_pop($result) : null;
	}
	// function get_by_path()

	/**
	 * @todo 意図がよく分からない
	 */
	public static function get_one_by_name_and_parent($name, $parent)
	{
		if ($parent) {
			$id_path = 'CONCAT("' . $parent->id_path . '", id, "/")';
		} else {
			$id_path = 'CONCAT(id, "/")';
		}
		$query = DB::select()
			->from(self::TABLE_NAME)
			->and_where_open()
			->and_where_open()
			->where('name', '=', $name['place'])
			->where('suffix', '=', $name['suffix'])
			->where('show_suffix', '=', true)
			->and_where_close()
			->or_where_open()
			->where('name', '=', $name['place'] . $name['suffix'])
			->where('show_suffix', '=', false)
			->or_where_close()
			->and_where_close()
			->where('deleted_at', '=', null);
		$query->where('id_path', '=', DB::expr($id_path));
		if (isset($name['identifier'])) {
			$query->where('identifier', '=', $name['identifier']);
		}

		$result = $query->as_object(Model_Division::class)->execute();
		if ($result->count()) {
			return $result[0];
		} else {
			return null;
		}
	}
	// function get_one_by_name_and_parent()

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
	 * @todo 意図がよく分からない
	 */
	public static function get_by_parent_division_and_date($parent, $date = null)
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
