<?php

class Presenter_Link extends Presenter_Layout
{
	public function view()
	{
		$this->title = '外部リンク';
		$this->description = '外部リンク';
		$this->og_type = 'article';
		$this->breadcrumbs = [
			'トップ' => Helper_Uri::create('top'),
			$this->title => '',
		];
		$this->nav_item = 'link';
	} // function view()
} // class Presenter_Link
