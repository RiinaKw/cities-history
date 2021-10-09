<?php

namespace MyApp\Abstracts;

use MyApp\Abstracts\ActiveRecord;
use Fuel\Core\Database_Query_Builder as Query;
use Fuel\Core\Database_Result_Cached as Result;

/**
 * データベースのテーブル操作に特化した基底クラス
 * （ActiveRecord 機能は一切持たない）
 *
 * @package  App\Abstracts
 */
abstract class Table
{
	/**
	 * 管理するテーブル名
	 * @var string
	 */
	public const TABLE_NAME = '';

	/**
	 * 管理するテーブルの主キー
	 * @var array<int, string>
	 */
	public const TABLE_PK = [];

	/**
	 * 管理するテーブルに紐づくアクティブレコードのクラス名
	 * @var string
	 */
	public const MODEL_NAME = '';

	/**
	 * 指定したキーでテーブルを検索
	 * @param  string     $column  テーブルのキー
	 * @param  string|int $value   検索する値
	 * @return ActiveRecord|null   見つかったモデル、見つからなければ null
	 */
	protected static function findBy(string $column, $value): ?ActiveRecord
	{
		$query = \DB::select()
			->from(static::TABLE_NAME)
			->where($column, $value)
			->where('deleted_at', null);

		$result = static::getAsModel($query);
		return $result[0];
	}

	/**
	 * クエリをモデルオブジェクトに変換
	 * @param  \Fuel\Core\Database_Query_Builder $query  検索クエリ
	 * @return \Fuel\Core\Database_Result_Cached         Fuel のデータベースキャッシュ
	 */
	public static function getAsModel(Query $query): Result
	{
		return $query->as_object(static::MODEL_NAME)->execute();
	}

	/**
	 * 指定したキーが一意であることを保証する
	 * @param string   $path       対象のパス
	 * @param int|null $ignore_id  除外するモデルの ID
	 * @throws \Exception  すでにパスが存在する場合
	 */

	/**
	 * 指定したキーが一意であることを保証する
	 * @param string      $column  テーブルのキー
	 * @param string|int  $value   検索する値
	 * @param int|null    $ignore_id  除外するモデルの ID, 除外するものがなければ null
	 * @throws \Exception  すでに値が存在している場合
	 */
	protected static function requireUnique(string $key, $value, int $ignore_id = null): void
	{
		$object = static::findBy($key, $value);
		if ($object && $object->id !== $ignore_id) {
			$table = static::TABLE_NAME;
			throw new \Exception("重複しています : {$table}.{$key} = '{$value}'");
		}
	}
}
