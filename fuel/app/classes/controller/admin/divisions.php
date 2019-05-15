<?php
/**
 * The Admin Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Admin_Divisions extends Controller_Admin_Base
{
	public function action_index()
	{
		$path = $this->param('path');
		if ($path)
		{
			$parent = Model_Division::get_by_path($path);
			$ids = Model_Division::get_by_parent_division_id_and_date($parent->id);
			array_unshift($ids, $parent->id);
		}
		else
		{
			$ids = Model_Division::get_all_id();

			$top_arr = Model_Division::get_top_level();
			$ids = [];
			foreach ($top_arr as $d)
			{
				$ids[] = $d->id;
			}
			foreach ($top_arr as $d)
			{
				$parent = Model_Division::find_by_pk($d->id);
				$temp = Model_Division::get_by_parent_division_id_and_date($d->id);
				$ids = array_merge($ids, $temp);
			}
		}

		$divisions = [];
		foreach ($ids as $id)
		{
			$division = Model_Division::find_by_pk($id);
			$division->path = $division->get_path(null, true);
			$division->url_detail = Helper_Uri::create('division.detail', ['path' => $division->path]);
			$end_event = Model_Event::find_by_pk($division->end_event_id);

			$division->valid_kana = $division->name_kana && $division->postfix_kana;
			$division->valid_start_event = !! $division->start_event_id;
			$division->valid_end_event = !! $division->end_event_id;
			$division->valid_government_code =
				($division->postfix == '郡')
				||
				$division->government_code
				||
				$end_event && strtotime($end_event->date) < strtotime('1970-04-01');

			$division->url_belongto = Helper_Uri::create('admin.divisions', ['path' => $division->path]);
			$divisions[] = $division;
		}

		// ビューを設定
		$content = View_Smarty::forge('admin/admin_divisions.tpl');
		$content->divisions = $divisions;

		$this->_set_view_var('content', $content);
		$this->_set_view_var('title', '自治体一覧');
		$this->_set_view_var('nav_item', 'division');
		$this->_set_view_var('breadcrumbs', ['一覧' => '']);
		return $this->_get_view();
	} // function action_index()
} // class Controller_Admin
