<?php

use MyApp\Abstracts\Controller;
use MyApp\Table\Division as DivisionTable;
use MyApp\Helper\Uri;

/**
 * The List Controller.
 *
 * @package  App\Controller
 * @extends  Controller_Base
 */
class Controller_List extends Controller
{
	public function action_index()
	{
		Uri::redirectPermanently('top');
	}
	// function action_index()

	public function action_detail()
	{
		$path = $this->param('path');

		Uri::redirectPermanently('division.tree', ['path' => $path]);
	}
	// function action_detail()
}
// class Controller_List
