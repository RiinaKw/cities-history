<?php

class Presenter_Top extends Presenter_Layout
{
	public function view()
	{
		$layout = $this->layout();

		foreach ($this->divisions as &$division)
		{
			$division->path = $division->get_path(null, true);
			$division->url_detail = Helper_Uri::create('division.detail', ['path' => $division->path]);
		}

		$components = [
			'add_division' => View_Smarty::forge('components/add_division.tpl'),
		];
		$this->components = $components;

		$layout->title = '都道府県一覧';
		$layout->description = '全国の都道府県一覧';
		$layout->og_type = 'article';

		$this->url_add = Helper_Uri::create('division.add');

		return $layout;
	}
}
