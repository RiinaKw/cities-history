<?php

namespace Fuel\Migrations;

use DB;
use DBUtil;

class Add_fk_division_id_to_event_details
{
	public function up()
	{
		// 外部キーの設定にあたり、「存在しない自治体を参照している詳細」を物理削除
		$query = DB::select('ed.*')
			->from(['event_details', 'ed'])
			->join(['divisions', 'dv'], 'left')
			->on('ed.division_id', '=', 'dv.id')
			->where('dv.id', null);
		$list = $query->execute()->as_array();
		echo 'Targets that must be deleted in event_details : ' . count($list) . "\n";
		foreach ($list as $detail) {
			DB::delete('event_details')->where('id', $detail['id'])->execute();
		}

		DBUtil::drop_index('event_details', 'idx_event_details_division_id');

		// 属性を合わせる（UNSIGNED）
		DBUtil::modify_fields('event_details', [
			'division_id' => [
				'constraint' => 11,
				'null' => true,
				'type' => 'int',
				'unsigned' => true,
			],
		]);

		// 外部キーを作成
		DBUtil::add_foreign_key('event_details', [
			'constraint' => 'fk__event_details__division_id',
			'key' => 'division_id',
			'reference' => [
				'table' => 'divisions',
				'column' => 'id',
			],
		]);
	}

	public function down()
	{
		DBUtil::drop_foreign_key('event_details', 'fk__event_details__division_id');
		DBUtil::create_index('event_details', 'division_id', 'idx_event_details_division_id');
	}
}