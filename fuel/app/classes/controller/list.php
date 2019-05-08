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
		$path = $this->param('path');
		$date = Input::get('date');
		$date = $date ? date('Y-m-d', strtotime($date)) : null;
		$top_division = null;
		if ($path)
		{
			$top_division = Model_Division::get_by_path($path);
			if ( ! $top_division || $top_division->get_path(null, true) != $path)
			{
				throw new HttpNotFoundException('自治体が見つかりません。');
			}
		}

		$divisions = [];
		if ($top_division)
		{
			$divisions[] = $top_division;
		}
		else
		{
			$divisions = Model_Division::get_top_level();
		}
		if ($top_division == null || $top_division && $top_division->postfix == '県')
		{
			foreach ($divisions as &$division)
			{
				$division->path = $division->get_path(null, true);
				$division->url_detail = Helper_Uri::create('division.detail', ['path' => $division->path]);

				$cities = Model_Division::get_by_postfix_and_date($division->id, '市', $date);
				foreach ($cities as &$city)
				{
					$city->path = $city->get_path(null, true);
					$city->url_detail = Helper_Uri::create('division.detail', ['path' => $city->path]);
				}
				$countries = Model_Division::get_by_postfix_and_date($division->id, '郡', $date);
				foreach ($countries as &$country)
				{
					$towns = Model_Division::get_by_parent_division_id_and_date($country->id, $date);
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
		}
		else
		{
			foreach ($divisions as &$division)
			{
				$division->path = $division->get_path(null, true);
				$division->url_detail = Helper_Uri::create('division.detail', ['path' => $division->path]);

				$towns = Model_Division::get_by_parent_division_id_and_date($division->id, $date);
				$towns_arr = [];
				foreach ($towns as $town_id)
				{
					$town = Model_Division::find_by_pk($town_id);
					$town->path = $town->get_path(null, true);
					$town->url_detail = Helper_Uri::create('division.detail', ['path' => $town->path]);

					$towns_arr[] = $town;
				}
				$division->cities = $towns_arr;
				$division->countries = [];
			}
		}
		$breadcrumbs_arr = Helper_Breadcrumb::breadcrumb_and_kana($path);
		$breadcrumbs = $breadcrumbs_arr['breadcrumbs'];
		$path_kana = $breadcrumbs_arr['path_kana'];

		// ビューを設定
		$content = View_Smarty::forge('list.tpl');
		$content->path = $path;
		$content->path_kana = $path_kana;
		$content->divisions = $divisions;
		$content->url_add = Helper_Uri::create('division.add');
		$content->url_all_list = Helper_Uri::create('list.index');

		$meiji_after = '1889-04-01';
		$content->meiji_after = [
			'date' => $meiji_after,
			'url' => Helper_Uri::create('list.division', ['path' => $path], ['date' => $meiji_after]),
		];
		$showa_before = '1953-10-01';
		$content->showa_before = [
			'date' => $showa_before,
			'url' => Helper_Uri::create('list.division', ['path' => $path], ['date' => $showa_before]),
		];
		$showa_after = '1961-04-01';
		$content->showa_after = [
			'date' => $showa_after,
			'url' => Helper_Uri::create('list.division', ['path' => $path], ['date' => $showa_after]),
		];
		$heisei_before = '1999-03-31';
		$content->heisei_before = [
			'date' => $heisei_before,
			'url' => Helper_Uri::create('list.division', ['path' => $path], ['date' => $heisei_before]),
		];
		$heisei_after = '2014-04-05';
		$content->heisei_after = [
			'date' => $heisei_after,
			'url' => Helper_Uri::create('list.division', ['path' => $path], ['date' => $heisei_after]),
		];
		$now = '2019-04-01';
		$content->now = [
			'date' => $now,
			'url' => Helper_Uri::create('list.division', ['path' => $path], ['date' => $now]),
		];

		$components = [
			'add_division' => View_Smarty::forge('components/add_division.tpl'),
		];
		$content->components = $components;

		$this->_set_view_var('content', $content);
		$this->_set_view_var('title', '自治体一覧');
		$this->_set_view_var('breadcrumbs', $breadcrumbs);
		return $this->_get_view();
	} // function action_index()

	public function action_search()
	{
		$q = Input::get('q');
		$result = Model_Division::search($q);

		foreach ($result as &$division)
		{
			$division->path = $division->get_path(null, true);
			$division->url_detail = Helper_Uri::create('division.detail', ['path' => $division->path]);
		}

		// ビューを設定
		$content = View_Smarty::forge('search.tpl');
		$content->divisions = $result;

		$this->_set_view_var('content', $content);
		$this->_set_view_var('title', '自治体検索');
		$this->_set_view_var('breadcrumbs', ['検索' => '']);
		return $this->_get_view();
	} // function action_search()
} // class Controller_List
