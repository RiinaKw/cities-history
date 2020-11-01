<?php

namespace Fuel\Migrations;

class Delete_kana_columns_from_divisions
{
	public function up()
	{
		\DBUtil::drop_fields('divisions', array(
			'fullname_kana',
		));
		\DBUtil::drop_fields('divisions', array(
			'path_kana',
		));
	}

	public function down()
	{
		\DBUtil::add_fields('divisions', array(
			'fullname_kana' => array('constraint' => 50,  'null' => false, 'type' => 'varchar'),
		));
		\DBUtil::add_fields('divisions', array(
			'path_kana' => array('constraint' => 200,  'null' => false, 'type' => 'varchar'),
		));
	}
}
