<?php
/**
 * The Rest Event Controller.
 *
 * @package  app
 * @extends  Controller_Rest
 */
class Controller_Rest_Event extends Controller_Rest
{
	public function get_detail($event_id)
	{
		$event = Model_Event::find_by_pk($event_id);

		if ( ! $event)
		{
			$response = array(
				'message' => 'イベントが見つかりません。',
			);
			return $this->response(
				$response,
				404
			);
		} // if ( ! $event)

		$divisions = Model_Event::get_relative_division($event_id);
		$response = [];
		foreach ($divisions as $division)
		{
			$response[] = [
				'id'        => $division->event_detail_id,
				'name'      => $division->name,
				'path'      => $division->get_path(null, true),
				'result'    => $division->result,
				'birth'     => ($division->start_event_id == $event->id),
				'death'     => ($division->end_event_id == $event->id),
				'geoshape'  => $division->geoshape,
				'is_refer'  => (int)$division->is_refer,
			];
		}
		return $this->response($response);
	} // function get_detail()
} // class Controller_Rest_Event
