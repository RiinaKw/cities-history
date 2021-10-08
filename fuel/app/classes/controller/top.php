<?php

use MyApp\Abstracts\Controller;
use MyApp\Table\Division as DivisionTable;

/**
 * The Top Controller.
 *
 * @package  App\Controller
 * @extends  Controller_Base
 */
class Controller_Top extends Controller
{
	public function action_index()
	{
		// create Presenter object
		$content = Presenter_Top::forge();
		$content->divisions = DivisionTable::get_top_level();

		return $content;
	}
	// function action_index()

	public function action_search()
	{
		$q = Helper_String::to_hiragana(Input::get('q'));

		$egg_q = strtolower($q);
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
