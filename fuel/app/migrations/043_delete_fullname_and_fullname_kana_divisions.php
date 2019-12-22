<?php

namespace Fuel\Migrations;

class Delete_fullname_and_fullname_kana_divisions
{
	public function up()
	{
		\DBUtil::drop_fields('divisions', array(
			'fullname',
			'fullname_kana'
		));
	}

	public function down()
	{
		\DBUtil::add_fields('divisions', array(
			'fullname'      => array('constraint' => 100,  'null' => false, 'type' => 'varchar'),
			'fullname_kana' => array('constraint' => 100,  'null' => false, 'type' => 'varchar'),
		));
	}
}
