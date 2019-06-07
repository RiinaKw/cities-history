<?php

namespace Fuel\Migrations;

class Add_is_unfinished_to_divisions
{
	public function up()
	{
		if ( ! \DBUtil::field_exists('divisions', array('is_unfinished')))
		{
			\DBUtil::add_fields('divisions', array(
				'is_unfinished' => array('null' => false, 'type' => 'boolean', 'default' => true),
			));
		}
	}

	public function down()
	{
		if (\DBUtil::field_exists('divisions', array('is_unfinished')))
		{
			\DBUtil::drop_fields('divisions', array(
				'is_unfinished',
			));
		}
	}
}
