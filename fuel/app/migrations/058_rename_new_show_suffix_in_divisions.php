<?php

namespace Fuel\Migrations;

class Rename_new_show_suffix_in_divisions
{
	public function up()
	{
		\DBUtil::modify_fields('divisions', array(
			'new_show_suffix' => array('null' => false, 'type' => 'boolean', 'default' => true, 'name' => 'show_suffix'),
		));
	}

	public function down()
	{
		\DBUtil::modify_fields('divisions', array(
			'show_suffix' => array('null' => false, 'type' => 'boolean', 'default' => true, 'name' => 'new_show_suffix'),
		));
	}
}
