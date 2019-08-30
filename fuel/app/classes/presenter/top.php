<?php

class Presenter_Top extends Presenter_Layout
{
	public function view()
	{
		foreach ($this->divisions as &$division)
		{
			$division->path = $division->get_path(null, true);
			$division->url_detail = Helper_Uri::create('division.detail', ['path' => $division->path]);
		}

		$components = [
			'add_division' => View_Smarty::forge('components/add_division.tpl'),
		];
		$this->components = $components;

		$this->title = '';
		$this->description = '全国の都道府県一覧';
		$this->og_type = 'website';

		$this->url_add = Helper_Uri::create('division.add');
	} // function view()
} // class Presenter_Top
