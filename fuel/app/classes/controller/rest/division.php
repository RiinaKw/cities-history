<?php
/**
 * The Rest Division Controller.
 *
 * @package  app
 * @extends  Controller_Rest
 */
class Controller_Rest_Division extends Controller_Rest
{
	public function get_list()
	{
		$start = microtime(true);

		$query = Input::get('query');

		$divisions = Model_Division::query($query);
		$sql = DB::last_query();
		$pathes = [];
		foreach ($divisions as $division)
		{
			$path = $division->get_path(null, true);
			if (strpos($path, $query) !== false)
			{
				$pathes[] = $path;
			}
		}
		usort($pathes, function($a, $b){
			return mb_strlen($a) < mb_strlen($b) ? -1 : (mb_strlen($a) > mb_strlen($b) ? 1 : 0);
		});

		$end = microtime(true);

		$response = [
			'time' => $end - $start,
			'sql' => $sql,
			'query' => $query,
			'suggestions' => $pathes,
		];
		return $this->response($response);
	} // function get_list()
} // class Controller_Rest_Division
