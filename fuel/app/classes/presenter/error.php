<?php

class Presenter_Error extends Presenter_Layout
{
	public function view()
	{
		$layout = $this->layout();

		$layout->title = $this->code;
		$layout->description = '';
		$layout->robots = 'noindex,nofollow';

		$this->url_add = Helper_Uri::create('division.add');

		return $layout;
	}
}
