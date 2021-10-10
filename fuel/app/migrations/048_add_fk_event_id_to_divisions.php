<?php

namespace Fuel\Migrations;

use DB;
use DBUtil;

class Add_fk_event_id_to_divisions
{
	protected static function indexExists(string $name): bool
	{
		$sql = 'SHOW INDEX FROM divisions WHERE Key_name = :idx';
		$query = DB::query($sql)->parameters([
			'idx' => $name,
		]);
		$result = $query->execute()->as_array();
		return (count($result) > 0);
	}

	public function up()
	{
		// 属性を合わせる（UNSIGNED）
		DBUtil::modify_fields('divisions', [
			'start_event_id' => [
				'constraint' => 11,
				'null' => true,
				'type' => 'int',
				'unsigned' => true,
			],
		]);
		DBUtil::modify_fields('divisions', [
			'end_event_id' => [
				'constraint' => 11,
				'null' => true,
				'type' => 'int',
				'unsigned' => true,
			],
		]);

		// 外部キーの設定にあたり、「存在しないイベントを参照している自治体」を NULL に変更
		$query = DB::select('dv.*')
			->from(['divisions', 'dv'])
			->join(['events', 'ev'], 'left')
			->on('dv.start_event_id', '=', 'ev.id')
			->where('ev.id', null)
			->where('dv.start_event_id', '!=', null);
		$list = $query->execute()->as_array();
		echo 'Targets where start_event_id should be null in divisions : ' . count($list) . "\n";
		foreach ($list as $detail) {
			var_dump($detail['id']);
			DB::update('divisions')
				->value('start_event_id', null)
				->where('id', $detail['id'])->execute();
		}

		$query = DB::select('dv.*')
			->from(['divisions', 'dv'])
			->join(['events', 'ev'], 'left')
			->on('dv.end_event_id', '=', 'ev.id')
			->where('ev.id', null)
			->where('dv.end_event_id', '!=', null);
		$list = $query->execute()->as_array();
		echo 'Targets where end_event_id should be null in divisions : ' . count($list) . "\n";
		foreach ($list as $detail) {
			DB::update('divisions')
				->value('end_event_id', null)
				->where('id', $detail['id'])->execute();
		}

		// 外部キーを作成
		$index = 'fk__divisions__start_event_id';
		if (! $this->indexExists($index)) {
			DBUtil::add_foreign_key('divisions', [
				'constraint' => $index,
				'key' => 'start_event_id',
				'reference' => [
					'table' => 'events',
					'column' => 'id',
				],
			]);
		}

		$index = 'fk__divisions__end_event_id';
		if (! $this->indexExists($index)) {
			DBUtil::add_foreign_key('divisions', [
				'constraint' => $index,
				'key' => 'start_event_id',
				'reference' => [
					'table' => 'events',
					'column' => 'id',
				],
			]);
		}
	}

	public function down()
	{
		$index = 'fk__divisions__start_event_id';
		if ($this->indexExists($index)) {
			DBUtil::drop_foreign_key('divisions', $index);
		}

		$index = 'fk__divisions__end_event_id';
		if ($this->indexExists($index)) {
			DBUtil::drop_foreign_key('divisions', $index);
		}
	}
}
