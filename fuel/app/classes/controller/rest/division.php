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
		$query = Input::get('query');

		$divisions = Model_Division::get_all();
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
		$response = [
			'query' => $query,
			'suggestions' => $pathes,
		];
		return $this->response($response);
	} // function get_list()
} // class Controller_Rest_Job_Stoppedreport
