<?php

/**
 * @package  App\Model
 */
class Model_Activity extends Model_Base
{
	protected static $_table_name	= 'activities';
	protected static $_primary_key	= ['id'];
	protected static $_created_at	= 'created_at';
	protected static $_updated_at	= 'updated_at';
	protected static $_deleted_at	= 'deleted_at';
	protected static $_mysql_timestamp = true;

	public static function insert_log($param)
	{
		$ip = Helper_Input::ip();
		$activity = self::forge([
			'user_id'    => $param['user_id'],
			'target'     => $param['target'],
			'target_id'  => $param['target_id'],
			'ip'         => $ip,
			'host'       => gethostbyaddr($ip),
			'user_agent' => Input::user_agent(),
		]);
		$activity->save();
	}
	// function insert_log()

	public static function get_log($param)
	{
		// initial params
		$default = array(
			'is_deleted'     => false,
			'per_page'       => 10,
			'pagination_url' => '',
		);
		$param = array_merge($default, $param);

		// query
		$query = DB::select()->from(self::$_table_name);
		$query->where('user_id', '=', $param['user_id']);
		if (! $param['is_deleted']) {
			// only that not been deleted
			$query->where('deleted_at', '=', null);
		}
		$count = $query->execute()->count();

		// create pagination
		$pagination_config = array(
			'pagination_url' => $param['pagination_url'],
			'num_links'      => 5,
			'per_page'       => $param['per_page'],
			'total_items'    => $count,
			'uri_segment'    => 'page',
		);
		$pagination = Pagination::forge('pagination_worker', $pagination_config);

		$query->limit($pagination->per_page);
		$query->offset($pagination->offset);

		$query->order_by('created_at', 'desc');

		$result = $query->as_object('Model_Activity')->execute()->as_array();
		$first = $pagination->offset + 1;
		$last = $first + count($result) - 1;

		return array(
			'result'     => $result,
			'count'      => $count,
			'first'      => $first,
			'last'       => $last,
			'pagination' => $pagination,
		);
	}
	// function get_log()
}
// class Model_Activity
