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
		$count = [];
		if ($top_division == null
			|| $top_division && ($top_division->postfix == '都' || $top_division->postfix == '府' || $top_division->postfix == '県'))
		{
			foreach ($divisions as &$division)
			{
				$count[$division->id] = $division->get_postfix_count($date);
				$division->path = $division->get_path(null, true);
				$division->url_detail = Helper_Uri::create('division.detail', ['path' => $division->path]);

				// 都道府県 > 市
				$cities = Model_Division::get_by_postfix_and_date($division->id, '市', $date);
				foreach ($cities as &$city)
				{
					$city->path = $city->get_path(null, true);
					$city->url_detail = Helper_Uri::create('division.detail', ['path' => $city->path]);

					// 都道府県 > 市 > 区
					$wards = Model_Division::get_by_postfix_and_date($city->id, '区', $date);
					$wards_count = $city->get_postfix_count($date);
					foreach ($wards as &$ward)
					{
						$ward->path = $ward->get_path(null, true);
						$ward->url_detail = Helper_Uri::create('division.detail', ['path' => $ward->path]);
					}
					$city->wards = $wards;
					$city->wards_count = $wards_count['区'];
				}

				// 都道府県 > 区
				$wards = Model_Division::get_by_postfix_and_date($division->id, '区', $date);
				foreach ($wards as &$ward)
				{
					$ward->path = $ward->get_path(null, true);
					$ward->url_detail = Helper_Uri::create('division.detail', ['path' => $ward->path]);
				}

				// 都道府県 > 郡
				$countries = Model_Division::get_by_postfix_and_date($division->id, '郡', $date);
				foreach ($countries as &$country)
				{
					$count[$country->id] = $country->get_postfix_count($date);
					if ($country->parent_division_id)
					{
						foreach ($count[$country->id] as $postfix => $postfix_count)
						{
							if (isset($count[$country->parent_division_id][$postfix]))
							{
								$count[$country->parent_division_id][$postfix] += $postfix_count;
							}
							else
							{
								$count[$country->parent_division_id][$postfix] = $postfix_count;
							}
						}
					}
					// 都道府県 > 郡 > 町村
					$towns = Model_Division::get_by_parent_division_id_and_date($country->id, $date);
					$towns_arr = [];
					foreach ($towns as $town_id)
					{
						$town = Model_Division::find_by_pk($town_id);
						$town->path = $town->get_path(null, true);
						$town->url_detail = Helper_Uri::create('division.detail', ['path' => $town->path]);

						$towns_arr[] = $town;
					}
					usort($towns_arr, function($a, $b){
						return strcmp($a->name_kana, $b->name_kana);
					});
					$country->path = $country->get_path(null, true);
					$country->url_detail = Helper_Uri::create('division.detail', ['path' => $country->path]);
					$country->towns = $towns_arr;
				}
				$division->cities = $cities;
				$division->wards = $wards;
				$division->countries = $countries;
			}
		}
		else
		{
			foreach ($divisions as &$division)
			{
				$division->path = $division->get_path(null, true);
				$division->url_detail = Helper_Uri::create('division.detail', ['path' => $division->path]);
				$count[$division->id] = $division->get_postfix_count($date);

				$towns = Model_Division::get_by_parent_division_id_and_date($division->id, $date);
				$towns_arr = [];
				foreach ($towns as $town_id)
				{
					$town = Model_Division::find_by_pk($town_id);
					$town->path = $town->get_path(null, true);
					$town->url_detail = Helper_Uri::create('division.detail', ['path' => $town->path]);

					$towns_arr[] = $town;
				}
				usort($towns_arr, function($a, $b){
					return strcmp($a->name_kana, $b->name_kana);
				});
				$division->cities = $towns_arr;
				$division->wards = [];
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
		$content->count = $count;
		$content->url_add = Helper_Uri::create('division.add');
		$content->url_all_list = Helper_Uri::create('list.index');

		$dates = Model_Referencedate::get_all();
		foreach ($dates as &$date)
		{
			$date->url = Helper_Uri::create('list.division', ['path' => $path], ['date' => $date->date]);
		}
		$content->reference_dates = $dates;
		$content->url_all = Helper_Uri::create('list.division', ['path' => $path]);

		$components = [
			'add_division' => View_Smarty::forge('components/add_division.tpl'),
		];
		$content->components = $components;

		$this->_set_view_var('content', $content);
		if ($path)
		{
			$this->_set_view_var('title', $path);
		}
		else
		{
			$this->_set_view_var('title', '自治体一覧');
		}
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
