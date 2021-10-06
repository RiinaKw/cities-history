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
	 * 削除フラグのカラム名
	 * @var string
	 */
	protected static $_deleted_at = 'deleted_at';

	/**
	 * 削除フラグをタイムスタンプにする
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

	public function is_deleted()
	{
		$property = static::$_deleted_at;
		return (bool)$this->$property;
	}
	// function is_deleted()
}
// class ActiveRecord
