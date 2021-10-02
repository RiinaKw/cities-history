<?php

namespace Fuel\Migrations;

class Add_search_path_to_divisions
{
	public function up()
	{
		\DBUtil::add_fields('divisions', array(
			'search_path' => array(
				'constraint' => 500,
				'null' => false, 'type' => 'varchar',
				'after' => 'identifier'
			),
			'search_path_kana' => array(
				'constraint' => 500,
				'null' => false, 'type' => 'varchar',
				'after' => 'search_path'
			),
		));
	}

	public function down()
	{
		\DBUtil::drop_fields('divisions', array(
			'search_path',
			'search_path_kana',
		));
	}
}
