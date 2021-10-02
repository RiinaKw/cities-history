<?php

namespace Fuel\Migrations;

class Rename_identify_to_identifier_in_divisions
{
	public function up()
	{
		if (\DBUtil::field_exists('divisions', array('identify'))) {
			\DBUtil::modify_fields('divisions', [
				'identify' => array('constraint' => 200,
				'null' => true,
				'type' => 'varchar',
				'name' => 'identifier'),
			]);
		}
	}

	public function down()
	{
		if (\DBUtil::field_exists('divisions', array('identifier'))) {
			\DBUtil::modify_fields('divisions', [
				'identifier' => [
					'constraint' => 100,
					'null' => false,
					'type' => 'varchar',
					'name' => 'identify'
				],
			]);
		}
	}
}
