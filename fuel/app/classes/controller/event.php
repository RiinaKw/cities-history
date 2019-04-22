<?php
/**
 * The Event Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Event extends Controller_Layout
{
	public function post_add()
	{
		// POST データを整形
		$arr = [];
		foreach (Input::post('id') as $key => $id)
		{
			$arr[] = [
				'id'       => Input::post('id.'.$key),
				'division' => Input::post('division.'.$key),
				'result'   => Input::post('result.'.$key),
				'birth'    => Input::post('birth.'.$key),
				'death'    => Input::post('death.'.$key),
				'delete'   => Input::post('delete.'.$key),
			];
		}

		try
		{
			DB::start_transaction();

			$event = Model_Event::forge([
				'date' => Input::post('date'),
				'type' => Input::post('type'),
			]);
			$event->save();

			foreach ($arr as $item)
			{
				$id = $item['id'];
				$divisions = Model_Division::set_path($item['division']);
				$division = array_pop($divisions);

				if ($item['delete'])
				{
					continue;
				}

				$detail = Model_Event_Detail::forge([
					'event_id' => $event->id,
					'division_id' => $division->id,
					'division_result' => $item['result'],
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

			DB::commit_transaction();
		}
		catch (Exception $e)
		{
			// 内部エラー
			DB::rollback_transaction();
			throw new HttpServerErrorException($e->getMessage());
		} // try

		Helper_Uri::redirect('division.detail', ['path' => Input::post('path')]);
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
			$arr[] = [
				'id'       => Input::post('id.'.$key),
				'division' => Input::post('division.'.$key),
				'result'   => Input::post('result.'.$key),
				'birth'    => Input::post('birth.'.$key),
				'death'    => Input::post('death.'.$key),
				'delete'   => Input::post('delete.'.$key),
			];
		}

		try
		{
			DB::start_transaction();

			$event->date = Input::post('date');
			$event->type = Input::post('type');
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
							'event_id' => $event->id,
							'division_id' => $division->id,
							'division_result' => $item['result'],
						]);
						$detail->save();
					}
					else
					{
						$detail = Model_Event_Detail::find_by_pk($id);
						$detail->division_result = $item['result'];
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
					$division->save();
				}

			} // foreach ($arr as $item)

			DB::commit_transaction();
		}
		catch (Exception $e)
		{
			// 内部エラー
			DB::rollback_transaction();
			throw new HttpServerErrorException($e->getMessage());
		} // try

		Helper_Uri::redirect('division.detail', ['path' => Input::post('path')]);
		return;
	} // function action_edit()

	public function action_delete($event_id)
	{
		$event = Model_Event::find_by_pk($event_id);
		if ( ! $event)
		{
			throw new HttpNotFoundException('イベントが見つかりません。');
		} // if ( ! $event)

		Debug::dump( $event_id, Input::post() );exit;
	} // function action_delete()
} // Controller_Event
