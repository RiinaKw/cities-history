<?php

/**
 * @package  App\Presenter
 */
class Presenter_Error extends Presenter_Layout
{
	public function view()
	{
		$this->title = $this->code;
		$this->description = '';
		$this->robots = 'noindex,nofollow';
		$this->show_share = false;
	}
	// function view()
}
// class Presenter_Error
