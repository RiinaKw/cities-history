<?php

/**
 * @package  App\Model
 */

namespace MyApp\Model;

use Orm\Model as Orm;

class Division extends Orm
{
	protected static $_table_name  = 'divisions';
	protected static $_primary_key = ['id'];
	protected static $_created_at  = 'created_at';
	protected static $_updated_at  = 'updated_at';
	protected static $_deleted_at  = 'deleted_at';
	protected static $_mysql_timestamp = true;

	protected static $_has_many = ['event_details'];
}
