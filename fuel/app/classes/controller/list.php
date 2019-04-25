<?php
/**
 * The List Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_List extends Controller_Layout
{
	public function action_index()
	{
		$divisions = Model_Division::find_all();
		$divisions_arr = [];
		foreach ($divisions as &$division)
		{
			$division->path = $division->get_path(null, true);
			$division->url_detail = Helper_Uri::create('division.detail', ['path' => $division->path]);

		}
		$divisions = Model_Division::get_top_level();
		foreach ($divisions as &$division)
		{
			$division->path = $division->get_path(null, true);
			$division->url_detail = Helper_Uri::create('division.detail', ['path' => $division->path]);

			$cities = Model_Division::get_by_postfix($division->id, '市');
			foreach ($cities as &$d)
			{
				$d->path = $d->get_path(null, true);
				$d->url_detail = Helper_Uri::create('division.detail', ['path' => $d->path]);
			}
			$countries = Model_Division::get_by_postfix($division->id, '郡');
			foreach ($countries as &$country)
			{
				$towns = Model_Division::get_by_parent_division_id($country->id);
				$towns_arr = [];
				foreach ($towns as $town_id)
				{
					$town = Model_Division::find_by_pk($town_id);
					$town->path = $town->get_path(null, true);
					$town->url_detail = Helper_Uri::create('division.detail', ['path' => $town->path]);

					$towns_arr[] = $town;
				}
				$country->path = $country->get_path(null, true);
				$country->url_detail = Helper_Uri::create('division.detail', ['path' => $country->path]);
				$country->towns = $towns_arr;
			}
			$division->cities = $cities;
			$division->countries = $countries;
		}

		// ビューを設定
		$content = View_Smarty::forge('list.tpl');
		$content->divisions = $divisions;

		$this->_set_view_var('content', $content);
		$this->_set_view_var('title', '自治体一覧');
		$this->_set_view_var('breadcrumbs', ['一覧' => '']);
		return $this->_get_view();
	} // function action_index()
} // class Controller_List
