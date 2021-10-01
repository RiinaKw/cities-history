<?php

class Model_Division extends Model_Base
{
	protected static $_table_name  = 'divisions';
	protected static $_primary_key = 'id';
	protected static $_created_at  = 'created_at';
	protected static $_updated_at  = 'updated_at';
	protected static $_deleted_at  = 'deleted_at';
	protected static $_mysql_timestamp = true;

	public function pmodel(): PresentationModel_Division
	{
		return new PresentationModel_Division($this);
	}

	public function validation(bool $is_new = false)
	{
		$validation = Validation::forge(mt_rand());

		// rules
		$field = $validation->add('name', '自治体名')
			->add_rule('required')
			->add_rule('max_length', 20);
		$field = $validation->add('name_kana', '自治体名かな')
			->add_rule('required')
			->add_rule('max_length', 20);
		$field = $validation->add('suffix', '自治体名種別')
			->add_rule('required')
			->add_rule('max_length', 20);
		$field = $validation->add('suffix_kana', '自治体名種別かな')
			->add_rule('required')
			->add_rule('max_length', 20);
		$field = $validation->add('identifier', '識別名')
			->add_rule('max_length', 50);
		$field = $validation->add('start_event_id',     '設置イベント');
		$field = $validation->add('end_event_id',       '廃止イベント');
		$field = $validation->add('government_code',    '全国地方公共団体コード')
			->add_rule('min_length', 6)
			->add_rule('max_length', 7);
		$field = $validation->add('display_order', '表示順')
			->add_rule('valid_string', array('numeric'));
		$field = $validation->add('source',     '出典');

		return $validation;
	} // function validation()

	public function get_source()
	{
		return Helper_Html::wiki($this->source);
	} // function get_source()

	public function get_tree($date)
	{
		$divisions = Table_Division::get_by_parent_division_and_date($this, $date);
		$tree = new Model_Division_Tree($this);
		return $tree->make_tree($divisions);
	}

	public function get_path(): string
	{
		if ($this->path) {
			return $this->path;
		} else {
			$id_arr = explode('/', $this->id_path);
			$name_arr = [];
			foreach ($id_arr as $id) {
				$id = (int)$id;
				if ($id) {
					$division = self::find_by_pk($id);
					$name_arr[] = $division->get_fullname();
				}
			}
			return implode('/', $name_arr);
		}
	} // function get_path()

	public function make_path(): string
	{
		$id_arr = explode('/', $this->id_path);
		$name_arr = [];
		foreach ($id_arr as $id) {
			$id = (int)$id;
			if ($id) {
				$division = self::find_by_pk($id);
				$name_arr[] = $division->get_fullname();
			}
		}
		return implode('/', $name_arr);
	} // function make_path()

	public function make_path_kana(): string
	{
		$id_arr = explode('/', $this->id_path);
		$kana_arr = [];
		foreach ($id_arr as $id) {
			$id = (int)$id;
			if ($id) {
				$division = self::find_by_pk($id);
				$kana_arr[] = $division->get_fullname_kana();
			}
		}
		return implode('/', $kana_arr);
	} // function make_path_kana()

	public function get_fullname_kana(): string
	{
		$kana = $this->name_kana;
		if ($this->show_suffix)
		{
			$kana .= '・'.$this->suffix_kana;
		}
		return $kana;
	} // function get_fullname_kana()

	public function get_search_fullname(): string
	{
		$name = $this->name;
		if ($this->show_suffix)
		{
			$name .= $this->suffix;
		}
		return $name;
	} // function get_search_fullname()

	public function get_search_fullname_kana(): string
	{
		$kana = $this->name_kana;
		if ($this->show_suffix)
		{
			$kana .= $this->suffix_kana;
		}
		return $kana;
	} // function get_search_fullname_kana()

	public function make_search_path(): string
	{
		$id_arr = explode('/', $this->id_path);
		$name_arr = [];
		foreach ($id_arr as $id) {
			$id = (int)$id;
			if ($id) {
				$division = self::find_by_pk($id);
				$name_arr[] = $division->get_search_fullname();
			}
		}
		return implode('', $name_arr);
	} // function make_path()

	public function make_search_path_kana(): string
	{
		$id_arr = explode('/', $this->id_path);
		$kana_arr = [];
		foreach ($id_arr as $id) {
			$id = (int)$id;
			if ($id) {
				$division = self::find_by_pk($id);
				$kana_arr[] = $division->get_search_fullname_kana();
			}
		}
		return implode('', $kana_arr);
	} // function make_path_kana()

	public function get_parent_path(): string
	{
		$path = $this->get_path();
		if (strpos($path, '/') === false) {
			return null;
		} else {
			return dirname($path);
		}
	} // function get_parent_path()

	public function get_belongs_path()
	{
		if ($this->belongs_division_id)
		{
			$division = self::find_by_pk($this->belongs_division_id);
			return $division->get_path();
		}
		else
		{
			return null;
		}
	} // function get_belongs_path()

	public function get_belongs_name()
	{
		if ($this->belongs_division_id)
		{
			$division = self::find_by_pk($this->belongs_division_id);
			return $division->get_fullname();
		}
		else
		{
			return null;
		}
	} // function get_belongs_name()

	public function get_fullname(): string
	{
		$name = $this->name;
		if ($this->show_suffix)
		{
			$name .= $this->suffix;
		}
		if ($this->identifier)
		{
			$name .= '('.$this->identifier.')';
		}
		return $name;
	} // function get_fullname()

	public function suffix_classification(): string
	{
		switch ($this->suffix)
		{
			default:
				return $this->suffix;

			case '町':
			case '村':
				return '町村';

			case '支庁':
			case '振興局':
			case '総合振興局':
				return '支庁';
		}
	}

	public function create($input)
	{
		$belongs = $input['belongs'] ?? null;
		$parent = $input['parent'] ?? null;
		$parent_division = self::get_by_path($parent);

		try
		{
			DB::start_transaction();

			$parent_division = null;
			if ($belongs)
			{
				$belongs_division = self::get_by_path($belongs);
				if ( ! $belongs_division)
				{
					$belongs_division = self::set_path($belongs);
					$belongs_division = array_pop($belongs_division);
				}
				$this->belongs_division_id = $belongs_division->id;
			}
			else
			{
				$this->belongs_division_id = null;
			}

			if (isset($input['name'])) {
				$this->name            = $input['name'];
			}
			if (isset($input['name_kana'])) {
				$this->name_kana       = Helper_String::to_hiragana($input['name_kana']);
				$this->is_empty_kana   = empty($input['name_kana']);
			}
			if (isset($input['suffix'])) {
				$this->suffix          = $input['suffix'];
			}
			if (isset($input['suffix_kana'])) {
				$this->suffix_kana     = Helper_String::to_hiragana($input['suffix_kana']);
			}
			if (isset($input['show_suffix'])) {
				$this->show_suffix     = !! $input['show_suffix'];
			}
			if (isset($input['government_code'])) {
				$this->government_code = Helper_Governmentcode::normalize($input['government_code']) ?: null;
				$this->is_empty_government_code = empty($input['government_code']);
			}
			if (isset($input['display_order']))
			{
				$this->display_order   = $input['display_order'] ?: null;
			}
			if (isset($input['is_unfinished']))
			{
				$this->is_unfinished   = !! $input['is_unfinished'];
			}
			if (isset($input['identifier']))
			{
				$this->identifier      = $input['identifier'] ?: null;
			}
			if (isset($input['source']))
			{
				$this->source          = $input['source'] ?: null;
			}
			$this->search_path = '';
			$this->search_path_kana = '';
			$this->save();

			$path = $parent . '/' . $this->get_fullname();
			$this->id_path = self::make_id_path($path, $this->id);

			$this->fullname         = $this->get_fullname();
			$this->path             = $this->make_path();

			$this->search_path      = $this->make_search_path();
			$this->search_path_kana = $this->make_search_path_kana();

			$query = DB::select()
				->from(self::$_table_name)
				->where('deleted_at', '=', null)
				->where('path', '=', $this->path)
				->where('id', '!=', $this->id)
				;
			if ($query->execute()->count())
			{
				throw new HttpBadRequestException('重複しています。');
			}
			$this->save();

			DB::commit_transaction();
		}
		catch (HttpBadRequestException $e)
		{
			// internal error
			DB::rollback_transaction();
			throw new HttpBadRequestException($e->getMessage());
		}
		catch (Exception $e)
		{
			Debug::dump($e, $e->getTraceAsString());exit;
			// internal error
			DB::rollback_transaction();
			throw new HttpServerErrorException($e->getMessage());
		}
	} // function create()

	public function dump(): void
	{
		echo $this->id_path, ' ', $this->path;
	}
} // class Model_Division
