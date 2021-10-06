<?php

/**
 * @package  App\Abstracts
 */

namespace MyApp\Abstracts;

use Orm\Model_Soft as Orm;

/**
 * Orm を継承したアクティブレコードの基底クラス
 */
abstract class ActiveRecord extends Orm
{
	/**
	 * テーブル名
	 * @var string
	 */
	protected static $_table_name	= '';

	/**
	 * 主キーのカラム名
	 * @var array<int, string>
	 */
	protected static $_primary_key	= ['id'];

	/**
	 * 作成日時のカラム名
	 * @var string
	 */
	protected static $_created_at  = 'created_at';

	/**
	 * 更新日時のカラム名
	 * @var string
	 */
	protected static $_updated_at  = 'updated_at';

	/**
	 * 削除日時のカラム名
	 * @var string
	 */
	protected static $_deleted_at = 'deleted_at';

	/**
	 * 作成・更新日時をタイムスタンプにする
	 * @var bool
	 */
	protected static $_mysql_timestamp = true;

	/**
	 * 削除日時をタイムスタンプにする
	 * @var bool
	 */
	protected static $_default_mysql_timestamp = true;

	/**
	 * 作成・更新時にタイムスタンプを追加
	 * @var array
	 */
	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => true,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => true,
		),
	);

	/**
	 * 削除済みかどうか
	 * @return boolean  削除されていれば true
	 * @todo これ Orm の機能にない？
	 */
	public function is_deleted(): bool
	{
		$property = static::$_deleted_at;
		return (bool)$this->$property;
	}
	// function is_deleted()
}
// class ActiveRecord
