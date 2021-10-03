<?php

/**
 * @package  App\Model
 */

use Orm\Model as Orm;

//abstract class Model_Base extends Model_Crud
abstract class Model_Base extends Orm
{
	protected static $_deleted_at = 'deleted_at';

	public function soft_delete()
	{
		$property = self::$_deleted_at;
		if (! $this->is_deleted()) {
			$this->$property = date('Y-m-d H:i:s');
			$this->save();
		}
	}
	// function soft_delete()

	public function is_deleted()
	{
		$property = self::$_deleted_at;
		return (bool)$this->$property;
	}
	// function is_deleted()
}
// class Model_Base
