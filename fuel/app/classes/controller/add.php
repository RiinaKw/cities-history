<?php
/**
 * The Add Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Add extends Controller_Layout
{
	public function action_index()
	{
		$path = $this->param('path');
		$divisions = Model_Division::set_path($path);
		foreach ($divisions as $div)
		{
			if ( ! $div->start_event)
			{
				$event = Model_Event::create([
					'date' => '1989-04-01',
					'type' => '新設合併',
				]);
				$div->start_event = $event->id;
				$div->save();
			}

			if ( ! $div->end_event)
			{
				$event = Model_Event::create([
					'date' => '2019-04-01',
					'type' => '存続',
				]);
				$div->end_event = $event->id;
				$div->save();
			}
		}
		Debug::dump($divisions);

		// ビューを設定
		$content = View_Smarty::forge('hello.tpl');

		$this->_set_view_var('content', $content);
		$this->_set_view_var('title', 'hello');
		return $this->_get_view();
	}
}
