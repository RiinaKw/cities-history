<?php

class Presenter_About extends Presenter_Layout
{
	public function view()
	{
		$this->title = 'Cities History Project について';
		$this->description = 'Cities History Project について';
		$this->og_type = 'article';
		$this->breadcrumbs = [
			'トップ' => Helper_Uri::create('top'),
			$this->title => '',
		];
		$this->nav_item = 'about';
	} // function view()
} // class Presenter_About
