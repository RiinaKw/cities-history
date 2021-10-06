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
	protected static $_deleted_at = 'deleted_at';
	protected static $_default_mysql_timestamp = true;

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
/*
	public function soft_delete()
	{
		$property = self::$_deleted_at;
		if (! $this->is_deleted()) {
			$this->$property = date('Y-m-d H:i:s');
			$this->save();
		}
	}
	// function soft_delete()
*/
	public function is_deleted()
	{
		$property = static::$_deleted_at;
		return (bool)$this->$property;
	}
	// function is_deleted()
}
// class ActiveRecord
