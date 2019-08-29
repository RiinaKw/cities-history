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
		$divisions = Model_Division::get_top_level();

		// ビューを設定
		$content = Presenter::forge('list/index', 'view', null, 'top.tpl');
		$content->divisions = $divisions;

		return $content;
	} // function action_index()

	public function action_detail()
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
		if ($top_division->top_parent_division_id)
		{
			$ids = Model_Division::get_by_parent_division_id_and_date($top_division->id, $date);
			foreach ($ids as $id)
			{
				$divisions[] = Model_Division::find_by_pk($id);
			}
		}
		else
		{
			$divisions = Model_Division::get_by_top_parent_division_id_and_date($top_division->id, $date);
		}
		$count = [
			'支庁' => 0,
			'区' => 0,
			'市' => 0,
			'郡' => 0,
			'町' => 0,
			'村' => 0,
		];
		$child_divisions = [];
		foreach ($divisions as $division)
		{
			$child_divisions[$division->id] = $division;

			if ( ! isset($count[$division->postfix]))
			{
				$count[$division->postfix] = 0;
			}
			$count[$division->postfix]++;
		}

		$ids_tree = [];
		foreach ($child_divisions as $child)
		{
			if ( ! isset($ids_tree[$child->parent_division_id]))
			{
				$ids_tree[$child->parent_division_id] = [
					'count' => [
						'区' => 0,
						'町' => 0,
						'村' => 0,
					],
					'children' => [],
				];
			}
			$ids_tree[$child->parent_division_id]['children'][$child->id] = $child->id;

			if ( ! isset($ids_tree[$child->parent_division_id]['count'][$child->postfix]))
			{
				$ids_tree[$child->parent_division_id]['count'][$child->postfix] = 0;
			}
			$ids_tree[$child->parent_division_id]['count'][$child->postfix]++;
		}
		foreach ($ids_tree[$top_division->id]['children'] as $id)
		{
			if (isset($ids_tree[$id]))
			{
				$tree = $ids_tree[$id];
				$ids_tree[$top_division->id]['children'][$id] = $tree;
				unset($ids_tree[$id]);
			}
		}

		$divisions_tree = [
			'区' => [],
			'市' => [],
			'郡' => [],
			'町村' => [],
		];
		foreach ($ids_tree[$top_division->id]['children'] as $id => $child)
		{
			$div = $child_divisions[$id];
			$postfix = $div->postfix;
			switch ($postfix)
			{
				case '支庁':
				case '区':
				case '市':
				case '郡':
				break;

				default:
					$postfix = '町村';
				break;
			} // swtich
			if (is_array($child))
			{
				$div->_count = $child['count'];
				$divisions_tree[$postfix][$id] = $div;
				foreach ($child['children'] as $town_id)
				{
					$town = $child_divisions[$town_id];
					$town_postfix = $town->postfix;
					switch ($town_postfix)
					{
						case '区':
						break;

						default:
							$town_postfix = '町村';
						break;
					} // swtich
					if ( ! isset($divisions_tree[$postfix][$id]->_children[$town_postfix]))
					{
						$divisions_tree[$postfix][$id]->_children[$town_postfix] = [];
					}
					$divisions_tree[$postfix][$id]->_children[$town_postfix][$town_id] = $town;
				} // foreach
			}
			else
			{
				$divisions_tree[$postfix][$id] = $div;
			}
		}

		// ビューを設定
		$content = Presenter::forge('list/detail', 'view', null, 'list.tpl');
		$content->date = $date;
		$content->division = $top_division;
		$content->tree = $divisions_tree;
		$content->count = $count;

		return $content;
	} // function action_detail()

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

		return $content;
	} // function action_search()
} // class Controller_List
