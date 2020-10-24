<?php
/**
 * The Event Controller.
 *
 * @package  app
 * @extends  Controller_Base
 */
class Controller_Event extends Controller_Base
{
	const SESSION_DIVISION_LIST = 'division';

	public function before()
	{
		parent::before();

		if ( ! $this->_user)
		{
			throw new HttpNoAccessException('permission denied');
		}
	} // function before()

	public function post_add()
	{
		// unify post data
		$arr = [];
		foreach (Input::post('id') as $key => $id)
		{
			if ( ! Input::post('division.'.$key))
			{
				continue;
			}
			$arr[] = [
				'order'    => Input::post('order.'.$key),
				'id'       => Input::post('id.'.$key),
				'division' => Input::post('division.'.$key),
				'result'   => Input::post('result.'.$key),
				'birth'    => Input::post('birth.'.$key),
				'death'    => Input::post('death.'.$key),
				'delete'   => Input::post('delete.'.$key),
				'geoshape' => Model_Event_Detail::unify_geoshape(Input::post('geoshape.'.$key)),
				'refer'    => Input::post('refer.'.$key),
			];
		}

		try
		{
			DB::start_transaction();

			$event = Model_Event::forge([
				'date' => Helper_Date::normalize(Input::post('date')),
				'title' => Input::post('title'),
				'comment' => Input::post('comment'),
				'source' => Input::post('source'),
			]);
			$event->save();

			foreach ($arr as $item)
			{
				$id = $item['id'];
				if ( ! $id)
				{
					continue;
				}
				$divisions = Model_Division::set_path($item['division']);
				$division = array_pop($divisions);

				if ($item['delete'])
				{
					continue;
				}

				$detail = Model_Event_Detail::forge([
					'order' => $item['order'],
					'event_id' => $event->id,
					'division_id' => $division->id,
					'result' => $item['result'],
					'geoshape' => Model_Event_Detail::unify_geoshape($item['geoshape']),
					'is_refer' => $item['refer'] ? true : false,
				]);
				$detail->save();

				if ($item['birth'])
				{
					$division->start_event_id = $event->id;
					$division->save();
				}
				if ($item['death'])
				{
					$division->end_event_id = $event->id;
					$division->save();
				}

			} // foreach ($arr as $item)

			Model_Activity::insert_log([
				'user_id' => Session::get('user_id'),
				'target' => 'add event',
				'target_id' => $event->id,
			]);

			DB::commit_transaction();
		}
		catch (Exception $e)
		{
			// internal error
			DB::rollback_transaction();
			throw new HttpServerErrorException($e->getMessage());
		} // try

		$url = Session::get(self::SESSION_DIVISION_LIST);
		Response::redirect($url);
		return;
	} // function post_add()

	public function action_edit($event_id)
	{
		$event = Model_Event::find_by_pk($event_id);
		if ( ! $event)
		{
			throw new HttpNotFoundException('イベントが見つかりません。');
		} // if ( ! $event)

		// POST データを整形
		$arr = [];
		foreach (Input::post('id') as $key => $id)
		{
			if ( ! Input::post('division.'.$key))
			{
				continue;
			}
			$arr[] = [
				'order'    => Input::post('order.'.$key),
				'id'       => Input::post('id.'.$key),
				'division' => Input::post('division.'.$key),
				'result'   => Input::post('result.'.$key),
				'birth'    => Input::post('birth.'.$key),
				'death'    => Input::post('death.'.$key),
				'delete'   => Input::post('delete.'.$key),
				'geoshape' => Model_Event_Detail::unify_geoshape(Input::post('geoshape.'.$key)),
				'refer'    => Input::post('refer.'.$key),
			];
			$geoshape = Model_Event_Detail::unify_geoshape(Input::post('geoshape.'.$key));
		}

		try
		{
			DB::start_transaction();

			$event->date = Input::post('date');
			$event->title = Input::post('title');
			$event->comment = Input::post('comment');
			$event->source = Input::post('source');
			$event->save();

			foreach ($arr as $item)
			{
				$id = $item['id'];
				$divisions = Model_Division::set_path($item['division']);
				$division = array_pop($divisions);

				if ($item['delete'])
				{
					if ($id != 'new')
					{
						$detail = Model_Event_Detail::find_by_pk($id);
						$detail->soft_delete();
					}
				}
				else
				{
					if ($id == 'new')
					{
						$detail = Model_Event_Detail::forge([
							'order' => $item['order'],
							'event_id' => $event->id,
							'division_id' => $division->id,
							'result' => $item['result'],
							'geoshape' => Model_Event_Detail::unify_geoshape($item['geoshape']),
							'is_refer' => $item['refer'] ? true : false,
						]);
						$detail->save();
					}
					else
					{
						$detail = Model_Event_Detail::find_by_pk($id);
						$detail->order = $item['order'];
						$detail->result = $item['result'];
						$detail->geoshape = $item['geoshape'];
						$detail->is_refer = $item['refer'] ? true : false;
						$detail->save();
					} // if ($id == 'new')
				} // if ($item['delete'])

				if ($item['birth'])
				{
					$division->start_event_id = $event->id;
					$division->save();
				}
				if ($item['death'])
				{
					$division->end_event_id = $event->id;
					$division->end_date = $event->date;
					$division->save();
				}
				else
				{
					$division->end_date = '9999-12-31';
					$division->save();
				}

			} // foreach ($arr as $item)

			Model_Activity::insert_log([
				'user_id' => Session::get('user_id'),
				'target' => 'edit event',
				'target_id' => $event->id,
			]);

			DB::commit_transaction();
		}
		catch (Exception $e)
		{
			// internal error
			DB::rollback_transaction();
			throw new HttpServerErrorException($e->getMessage());
		} // try

		$url = Session::get(self::SESSION_DIVISION_LIST);
		Response::redirect($url);
		return;
	} // function action_edit()

	public function action_delete($event_id)
	{
		$event = Model_Event::find_by_pk($event_id);
		if ( ! $event)
		{
			throw new HttpNotFoundException('イベントが見つかりません。');
		} // if ( ! $event)

		$event->delete();

		Model_Activity::insert_log([
			'user_id' => Session::get('user_id'),
			'target' => 'delete event',
			'target_id' => $event->id,
		]);

		Debug::dump( $event_id, Input::post() );exit;
	} // function action_delete()
} // Controller_Event
