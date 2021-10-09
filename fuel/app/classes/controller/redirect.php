<?php

use MyApp\Abstracts\Controller;
use MyApp\Helper\Uri;

/**
 * The Redirect Controller.
 *
 * @package  Fuel\Controller
 * @extends  MyApp\Abstracts\Controller
 */
class Controller_Redirect extends Controller
{
	public function action_list_index()
	{
		Uri::redirectPermanently('top');
	}

	public function action_list_detail()
	{
		Uri::redirectPermanently(
			'division.tree',
			[
				'path' => $this->param('path'),
			]
		);
	}

	public function action_division_children()
	{
		Uri::redirectPermanently(
			'division.children',
			[
				'path' => $this->param('path')
			],
			[
				'label' => Input::get('label'),
				'start' => Input::get('start'),
				'end' => Input::get('end'),
			]
		);
	}

	public function action_division_detail()
	{
		Uri::redirectPermanently(
			'division.detail',
			[
				'path' => $this->param('path')
			]
		);
	}
}
