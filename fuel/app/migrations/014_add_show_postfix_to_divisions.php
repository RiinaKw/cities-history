<?php

namespace Fuel\Migrations;

class Add_show_postfix_to_divisions
{
	public function up()
	{
		if (! \DBUtil::field_exists('divisions', array('show_postfix'))) {
			\DBUtil::add_fields('divisions', array(
				'show_postfix' => array('null' => false, 'type' => 'boolean', 'default' => true),
			));
		}
	}

	public function down()
	{
		if (\DBUtil::field_exists('divisions', array('show_postfix'))) {
			\DBUtil::drop_fields('divisions', array(
				'show_postfix',
			));
		}
	}
}
