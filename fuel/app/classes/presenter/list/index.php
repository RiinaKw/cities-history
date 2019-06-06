<?php

class Presenter_List_Index extends Presenter_Layout
{
	protected function _get_path($arr)
	{
		foreach ($arr as $item)
		{
			$item->path = $item->get_path(null, true);
			$item->url_detail = Helper_Uri::create(
				'division.detail',
				['path' => $item->path]
			);

			if (isset($item->children))
			{
				foreach ($item->children as $children)
				{
					$this->_get_path($children);
				}
			}
			if (isset($item->wards))
			{
				$this->_get_path($item->wards);
			}
			if (isset($item->towns))
			{
				$this->_get_path($item->towns);
			}
		}
	} // function _get_path()

	public function view()
	{
		$this->_get_path($this->divisions);

		$dates = Model_Referencedate::get_all();
		foreach ($dates as &$cur_date)
		{
			$cur_date->url = Helper_Uri::create(
				'list.division',
				['path' => $this->path],
				['date' => $cur_date->date]
			);
		}
		$this->reference_dates = $dates;
		$this->url_all = Helper_Uri::create(
			'list.division',
			['path' => $this->path]
		);
		$this->url_add = Helper_Uri::create('division.add');

		$components = [
			'add_division' => View_Smarty::forge('components/add_division.tpl'),
		];
		$this->components = $components;

		if ($this->path)
		{
			$title = $this->path.'の自治体一覧';
			$description = $this->path.'の自治体一覧';
		}
		else
		{
			$title = '自治体一覧';
			$description = '全国の自治体一覧';
		}
		if ($this->date)
		{
			$description .= Helper_Date::date(' Y(Jk)-m-d', $this->date);
		}

		$breadcrumbs_arr = Helper_Breadcrumb::breadcrumb_and_kana($this->path);
		$this->path_kana = $breadcrumbs_arr['path_kana'];

		$this->title = $title;
		$this->description = $description;
		$this->og_type = 'article';
		$this->breadcrumbs = $breadcrumbs_arr['breadcrumbs'];

		$this->url_add = Helper_Uri::create('division.add');
	} // function view()
} // class Presenter_List_Index
