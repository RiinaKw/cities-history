<?php

class Presenter_Admin_Divisions_List extends Presenter_Layout
{
	public function view()
	{
		$layout = $this->layout();

		foreach ($this->divisions as $division)
		{
			$division->path = $division->get_path(null, true);
			$division->url_detail = Helper_Uri::create('division.detail', ['path' => $division->path]);
			$division->url_belongto = Helper_Uri::create('admin.divisions', ['path' => $division->path]);
		}

		$layout->title = '自治体一覧';
		$layout->nav_item = 'division';
		$layout->breadcrumbs = ['一覧' => ''];

		return $layout;
	}
}
