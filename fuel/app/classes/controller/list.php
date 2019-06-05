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
class Controller_List extends Controller_Base
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
			|| $top_division && (
				$top_division->postfix == '都'
				|| $top_division->postfix == '道'
				|| $top_division->postfix == '府'
				|| $top_division->postfix == '県'
			)
		)
		{
			foreach ($divisions as &$division)
			{
				$count[$division->id] = $division->get_postfix_count($date);

				// 都道府県直下
				$ids = Model_Division::get_by_parent_division_id_and_date($division->id, $date);
				$child_divisions = [
					'支庁' => [],
					'区' => [],
					'市' => [],
					'郡' => [],
					'町村' => [],
				];
				foreach ($ids as $id)
				{
					$d = Model_Division::find_by_pk($id);
					if ($d->parent_division_id == $division->id)
					{
						$postfix = $d->postfix;
						if ($postfix == '町' || $postfix == '村')
						{
							$postfix = '町村';
						}
						$child_divisions[$postfix][] = $d;
					}
				}

				// 都道府県 > 市
				foreach ($child_divisions['市'] as &$city)
				{
					// 都道府県 > 市 > 区
					$wards = Model_Division::get_by_postfix_and_date($city->id, '区', $date);
					$wards_count = $city->get_postfix_count($date);
					if ($wards)
					{
						$city->wards = $wards;
						$city->wards_count = $wards_count['区'];
					}
				}

				// 都道府県 > 郡
				foreach ($child_divisions['郡'] as &$country)
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

						$towns_arr[] = $town;
					}
					usort($towns_arr, function($a, $b){
						return strcmp($a->name_kana, $b->name_kana);
					});
					$country->towns = $towns_arr;
				}
				$division->children = $child_divisions;
			}
		}
		else
		{
			foreach ($divisions as &$division)
			{
				$count[$division->id] = $division->get_postfix_count($date);

				$ids = Model_Division::get_by_parent_division_id_and_date($division->id, $date);
				$child_divisions = [
					'支庁' => [],
					'区' => [],
					'市' => [],
					'郡' => [],
					'町村' => [],
				];
				foreach ($ids as $id)
				{
					$d = Model_Division::find_by_pk($id);
					if ($d->parent_division_id == $division->id || $d->belongs_division_id == $division->id)
					{
						$postfix = $d->postfix;
						if ($postfix != '支庁' && $postfix != '区' && $postfix != '市' && $postfix != '郡')
						{
							$postfix = '町村';
						}
						$child_divisions[$postfix][] = $d;
					}
				}
				$division->children = $child_divisions;
			}
		}

		// ビューを設定
		$content = Presenter::forge('list/index', 'view', null, 'list.tpl');
		$content->date = $date;
		$content->path = $path;
		$content->divisions = $divisions;
		$content->count = $count;

		return $content->view();
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
		$content = Presenter::forge('list/search', 'view', null, 'search.tpl');
		$content->divisions = $result;
		$content->q = $q;

		return $content->view();
	} // function action_search()
} // class Controller_List
