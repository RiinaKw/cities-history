<?php

namespace Fuel\Migrations;

class Add_fullname_to_divisions
{
	public function up()
	{
		if (! \DBUtil::field_exists('divisions', array('fullname'))) {
			\DBUtil::add_fields('divisions', array(
				'fullname'      => array('constraint' => 50,  'null' => false, 'type' => 'varchar'),
				'fullname_kana' => array('constraint' => 50,  'null' => false, 'type' => 'varchar'),
			));
		}
		\DBUtil::create_index('divisions', 'fullname', 'idx_divisions_fullname', 'fulltext');
		\DBUtil::create_index('divisions', 'fullname_kana', 'idx_divisions_fullname_kana', 'fulltext');
	}

	public function down()
	{
		if (\DBUtil::field_exists('divisions', array('fullname'))) {
			\DBUtil::drop_fields('divisions', array(
				'fullname',
				'fullname_kana',
			));
		}
	}
}
