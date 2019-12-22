<?php

namespace Fuel\Migrations;

class Add_new_show_suffix_to_divisions
{
	public function up()
	{
		if ( ! \DBUtil::field_exists('divisions', array('new_show_suffix')))
		{
			\DBUtil::add_fields('divisions', array(
				'new_show_suffix' => array('null' => false, 'type' => 'boolean', 'default' => true, 'after' => 'suffix_kana'),
			));
		}
	}

	public function down()
	{
		if ( \DBUtil::field_exists('divisions', array('new_show_suffix')))
		{
			\DBUtil::drop_fields('divisions', array(
				'new_show_suffix'
			));
		}
	}
}
