<?php

namespace Fuel\Migrations;

class Add_path_and_path_kana_to_divisions
{
	public function up()
	{
		if (! \DBUtil::field_exists('divisions', array('path'))) {
			\DBUtil::add_fields('divisions', array(
				'path' => array('constraint' => 200,  'null' => false, 'type' => 'varchar', 'after' => 'fullname_kana'),
			));
		}
		if (! \DBUtil::field_exists('divisions', array('path_kana'))) {
			\DBUtil::add_fields('divisions', array(
				'path_kana' => array('constraint' => 200,  'null' => false, 'type' => 'varchar', 'after' => 'path'),
			));
		}
	}

	public function down()
	{
		if (\DBUtil::field_exists('divisions', array('path'))) {
			\DBUtil::drop_fields('divisions', array(
				'path',
			));
		}
		if (\DBUtil::field_exists('divisions', array('path_kana'))) {
			\DBUtil::drop_fields('divisions', array(
				'path_kana',
			));
		}
	}
}
