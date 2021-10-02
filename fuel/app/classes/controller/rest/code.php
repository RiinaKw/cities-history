<?php

/**
 * The Rest Code Controller.
 *
 * @package  App\Controller
 * @extends  Controller_Rest
 */
class Controller_Rest_Code extends Controller_Rest
{
	public function get_detail($code)
	{
		try {
			$normalized = Helper_Governmentcode::normalize($code);
		} catch (Exception $e) {
			$response = [
				'error' => $e->getMessage(),
			];
			return $this->response($response, 400);
		}

		$divisions = Model_Division::find_by_government_code($normalized);

		$arr = [];
		if ($divisions) {
			foreach ($divisions as $division) {
				$arr[] = static::division_to_array($division);
			}
		}

		$status = 300;
		switch (count($arr)) {
			case 0:
				$status = 404;
				break;
			case 1:
				$status = 200;
				break;
		}
		$response = [
			'code' => $normalized,
			'divisions' => $arr,
		];
		return $this->response($response, $status);
	}

	protected function division_to_array($division)
	{
		$birth_event = Model_Event::find_by_pk($division->start_event_id);
		$death_event = Model_Event::find_by_pk($division->end_event_id);

		$birth_date = null;
		$death_date = null;
		if ($birth_event) {
			$birth_date = $birth_event->date;
		}
		if ($death_event) {
			$death_date = $death_event->date;
		}

		return [
			'code' => $division->government_code,
			'name' => [
				'body' => $division->name,
				'suffix' => $division->suffix,
			],
			'kana' => [
				'body' => $division->name_kana,
				'suffix' => $division->suffix_kana,
			],
			'birth' => $birth_date,
			'death' => $death_date,
			'path' => $division->path,
		];
	}
}
