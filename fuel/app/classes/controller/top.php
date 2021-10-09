<?php

use MyApp\Abstracts\Controller;
use MyApp\Table\Division as DivisionTable;
use MyApp\Helper\Str;

/**
 * The Top Controller.
 *
 * @package  Fuel\Controller
 * @extends  MyApp\Abstracts\Controller
 */
class Controller_Top extends Controller
{
	public function action_index()
	{
		// create Presenter object
		$content = Presenter_Top::forge();
		$content->divisions = DivisionTable::topLevel();

		return $content;
	}
	// function action_index()

	public function action_search()
	{
		$egg_q = strtolower(Input::get('q'));
		$eggs = [
			'coffee' => [
				'code' => 418,
				'message' => '418 I\'m a teapot : おれはやかんだ (Easter Egg)',
			],
			'game' => [
				'code' => 402,
				'message' => '402 Payment Required : いくら溶かした？ (Easter Egg)',
			],
		];
		foreach ($eggs as $key => $config) {
			if (strpos($egg_q, $key) !== false) {
				$code = $config['code'];
				$view = Presenter_Error::forge();
				$view->code = $code;
				$view->message = $config['message'];
				return Response::forge($view, $code);
			}
		}

		$q = Str::convertKana(Input::get('q'));
		$result = DivisionTable::search($q);

		// create Presenter object
		$content = Presenter_Search::forge();
		$content->divisions = $result;
		$content->q = $q;

		return $content;
	}
	// function action_search()

	public function action_session_clear()
	{
		$division = Session::get('division');
		Session::destroy();
		Session::set('division', $division);

		Response::redirect($division);
	}
}
// class Controller_Top
