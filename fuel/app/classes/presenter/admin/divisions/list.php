<?php

class Presenter_Admin_Divisions_List extends Presenter_Layout
{
	public function view()
	{
		foreach ($this->divisions as $division)
		{
			$division->path = $division->get_path(null, true);
			$division->url_detail = Helper_Uri::create('division.detail', ['path' => $division->path]);
			$division->url_belongto = Helper_Uri::create('admin.divisions', ['path' => $division->path]);
		}

		$this->title = '自治体一覧';
		$this->nav_item = 'division';
		$this->breadcrumbs = ['一覧' => ''];
	} // function view()
} // class Presenter_Admin_Divisions_List
