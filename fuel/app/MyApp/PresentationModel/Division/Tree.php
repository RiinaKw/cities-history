<?php

/**
 * @package  App\PresentationModel
 */

namespace MyApp\PresentationModel\Division;

use MyApp\Model\Division\Tree as Model;

class Tree
{
	protected $model = null;

	public function __construct(Model $model)
	{
		$this->model = $model;
	}

	public function suffiexes(): string
	{
		$string = '';
		foreach ($this->model->suffixes() as $suffix => $count) {
			if ($count) {
				$string .= $count . $suffix;
			}
		}
		return $string;
	}
}