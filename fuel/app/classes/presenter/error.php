<?php

class Presenter_Error extends Presenter_Layout
{
	public function view()
	{
		$this->title = $this->code;
		$this->description = '';
		$this->robots = 'noindex,nofollow';

		$this->url_add = Helper_Uri::create('division.add');
	} // function view()
} // class Presenter_Error
