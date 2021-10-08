<?php

use MyApp\Abstracts\Controller;
use MyApp\Helper\Uri;

/**
 * The Redirect Controller.
 *
 * @package  App\Controller
 * @extends  Controller_Base
 */
class Controller_Redirect extends Controller
{
	public function action_list_index()
	{
		Uri::redirectPermanently('top');
	}

	public function action_list_detail()
	{
		$path = $this->param('path');
		Uri::redirectPermanently('division.tree', ['path' => $path]);
	}
}
