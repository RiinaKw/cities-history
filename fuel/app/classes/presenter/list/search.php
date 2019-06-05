<?php

class Presenter_List_Search extends Presenter_Layout
{
	public function view()
	{
		$layout = $this->layout();

		$layout->title = '自治体検索';
		$layout->description = '自治体検索検索結果 : '.$this->q;
		$layout->robots = 'noindex,nofollow';
		$layout->og_type = 'article';
		$layout->breadcrumbs = ['検索' => ''];

		return $layout;
	} // function view()
} // class Presenter_List_Index
