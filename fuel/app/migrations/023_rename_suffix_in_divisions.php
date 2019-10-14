<?php

namespace Fuel\Migrations;

class Rename_suffix_in_divisions
{
	public function up()
	{
		\DBUtil::modify_fields('divisions', array(
			'postfix' => array('constraint' => 20,  'null' => false, 'type' => 'varchar', 'name' => 'suffix'),
			'postfix_kana' => array('constraint' => 20,  'null' => false, 'type' => 'varchar', 'name' => 'suffix_kana'),
			'show_postfix' => array('null' => false, 'type' => 'boolean', 'default' => true, 'name' => 'show_suffix'),
		));
	}

	public function down()
	{
		\DBUtil::modify_fields('divisions', array(
			'suffix' => array('constraint' => 20,  'null' => false, 'type' => 'varchar', 'name' => 'postfix'),
			'suffix_kana' => array('constraint' => 20,  'null' => false, 'type' => 'varchar', 'name' => 'postfix_kana'),
			'show_suffix' => array('null' => false, 'type' => 'boolean', 'default' => true, 'name' => 'show_postfix'),
		));
	}
}
