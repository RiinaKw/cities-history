<?php

namespace Fuel\Migrations;

use DB;
use DBUtil;
use Model_Division;

class Add_fk_belongs_id_to_divisions
{
	public function up()
	{
		// 属性を合わせる（UNSIGNED）
		DBUtil::modify_fields('divisions', [
			'belongs_division_id' => [
				'constraint' => 11,
				'null' => true,
				'type' => 'int',
				'unsigned' => true,
			],
		]);

		// 外部キーを作成
		DBUtil::add_foreign_key('divisions', [
			'constraint' => 'fk__divisions__belongs_division_id',
			'key' => 'belongs_division_id',
			'reference' => [
				'table' => 'divisions',
				'column' => 'id',
			],
		]);
	}

	public function down()
	{
		DBUtil::drop_foreign_key('divisions', 'fk__divisions__belongs_division_id');
	}
}
