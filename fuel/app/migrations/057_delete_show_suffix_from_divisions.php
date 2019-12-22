<?php

namespace Fuel\Migrations;

class Delete_show_suffix_from_divisions
{
	public function up()
	{
		if ( \DBUtil::field_exists('divisions', array('show_suffix')))
		{
			\DBUtil::drop_fields('divisions', array(
				'show_suffix'
			));
		}
	}

	public function down()
	{
		if ( ! \DBUtil::field_exists('divisions', array('show_suffix')))
		{
			\DBUtil::add_fields('divisions', array(
				'show_suffix' => array('null' => false, 'type' => 'boolean', 'default' => true),
			));
		}
	}
}
