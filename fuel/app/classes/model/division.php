<?php

class Model_Division extends Model_Base
{
	const RE_SUFFIX = '/^(?<place>.+?)(?<suffix>都|府|県|支庁|庁|総合振興局|振興局|市|郡|区|町|村|郷|城下|駅|宿|新宿|組|新田|新地)(\((?<identifier>.+?)\))?$/';

	protected static $_table_name  = 'divisions';
	protected static $_primary_key = 'id';
	protected static $_created_at  = 'created_at';
	protected static $_updated_at  = 'updated_at';
	protected static $_deleted_at  = 'deleted_at';
	protected static $_mysql_timestamp = true;

	public $_children = [];
	public $_count = [];

	public function validation($is_new = false, $factory = null)
	{
		$validation = Validation::forge($factory);

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

	protected static function _get_id($obj)
	{
		return $obj ? $obj->id : null;
	} // function _get_id()

	public function get_source()
	{
		return Helper_Html::wiki($this->source);
	} // function get_source()

	public static function get_suffix($name)
	{
		$result = preg_match(static::RE_SUFFIX, $name, $matches);
		return $result ? $matches['suffix'] : '';
	} // function get_suffix()

	public static function get_all_id()
	{
		$query = DB::select('id')
			->from(self::$_table_name)
			->where('deleted_at', '=', null)
			->order_by('name_kana', 'ASC');

		$result = $query->execute();
		$arr = [];
		foreach ($result as $item)
		{
			$arr[] = $item['id'];
		}
		return $arr;
	} // function get_all_id()

	public static function query($q)
	{
		$query = DB::select()
			->from(self::$_table_name)
			->where('deleted_at', '=', null)
			//->where(DB::expr('MATCH(fullname)'), 'AGAINST', DB::expr('(\'+'.$q.'\' IN BOOLEAN MODE)'));
			->where('fullname', 'LIKE', '%'.$q.'%');

		return $query->as_object('Model_Division')->execute();
	} // function query()

	public static function search($q)
	{
		$q = str_replace(array('\\', '%', '_'), array('\\\\', '\%', '\_'), $q);
		$q_arr = preg_split('/(\s+)|(　+)/', $q);
		$query = DB::select()
			->from(self::$_table_name)
			->where('deleted_at', '=', null);
		foreach ($q_arr as $word)
		{
			$query->and_where_open()
				->where('search_path', 'LIKE', '%'.$word.'%')
				->or_where('search_path_kana', 'LIKE', '%'.$word.'%')
				->and_where_close();
		}
		$query
			->order_by('is_empty_government_code', 'asc')
			->order_by('government_code', 'asc')
			->order_by('is_empty_kana', 'asc')
			->order_by('name_kana', 'asc')
			->order_by('end_date', 'desc');

		return $query->as_object('Model_Division')->execute();
	} // function search()

	public static function set_path($path)
	{
		$arr = explode('/', $path);
		$parent = null;
		$divisions = [];
		foreach ($arr as $name)
		{
			if ( ! $name)
			{
				throw new Exception('自治体名が入力されていません');
			}
			preg_match(static::RE_SUFFIX, $name, $matches);
			if ( ! $matches)
			{
				$matches = [
					'place' => $name,
					'suffix' => '',
				];
			}
			if ( ! $division = self::get_one_by_name_and_parent($matches, $parent))
			{
				$division = self::forge([
					'id_path' => '',
					'name' => $matches['place'],
					'name_kana' => '',
					'suffix' => $matches['suffix'],
					'suffix_kana' => '',
					'fullname' => '',
					'path' => '',
					'show_suffix' => true,
					'identifier' => (isset($matches['identifier']) ? $matches['identifier'] : null),
					'is_unfinished' => true,
					'is_empty_government_code' => true,
					'is_empty_kana' => true,
					'search_path' => '',
					'search_path_kana' => '',
					'end_date' => '9999-12-31',
					'source' => '',
				]);

				$division->save();

				$division->id_path = self::make_id_path($path, $division->id);

				$division->fullname         = $division->get_fullname();
				$division->path             = $division->make_path();

				$division->search_path      = $division->make_search_path();
				$division->search_path_kana = $division->make_search_path_kana();

				$division->save();

				Model_Activity::insert_log([
					'user_id' => Session::get('user_id'),
					'target' => 'add division',
					'target_id' => $division->id,
				]);
			}
			$divisions[] = $division;
			$parent = $division;
		}
		return $divisions;
	} // function set_path()

	public static function set_path_as_array($arr)
	{
		foreach ($arr as $item) {
			if (trim($item['path']) === '') {
				continue;
			}
			$divisions = self::set_path($item['path']);

			$division = array_pop($divisions);
			$division->name_kana = $item['name_kana'] ?: null;
			$division->suffix_kana = $item['suffix_kana'] ?: null;
			$division->government_code = $item['code'] ?: null;

			$division->fullname      = $division->get_fullname();
			$division->path          = $division->make_path();

			$division->save();
		}
	} // function set_path_as_array()

	public static function make_id_path($path, $self_id)
	{
		$parents = [];
		$cur_path = $path;
		while ($path) {
			$parent = dirname($path);
			if ($parent === '\\' || $parent === '/' || $parent === '.') {
				break;
			}
			$parents[] = $parent;
			if (strpos($parent, '/') === false) {
				break;
			}
			$path = $parent;
		}
		$parents = array_reverse($parents);

		$id_arr = [];
		foreach ($parents as $parent_path) {
			$d = self::get_by_path($parent_path);
			if ($d) {
				$id_arr[] = $d->id;
			}
		}
		$id_arr[] = $self_id;
		return implode('/', $id_arr) . '/';
	} // function make_id_path()

	public static function get_by_path($path)
	{
		$arr = explode('/', $path);
		$division = null;
		$parent = null;

		foreach ($arr as $name)
		{
			preg_match(static::RE_SUFFIX, $name, $matches);

			if ($matches)
			{
				$result = self::get_one_by_name_and_parent($matches, $parent);
				if ($result)
				{
					$division = $result;
					$parent = $division;
				}
				else
				{
					$division = null;
					$parent = null;
					break;
				}
			}
			else
			{
				$matches = array(
					'place' => $name,
					'suffix' => '',
				);
				$result = self::get_one_by_name_and_parent($matches, $parent);
				if ($result)
				{
					$division = $result;
					$parent = $division;
				}
				else
				{
					$division = null;
					$parent = null;
					break;
				}
			}
		}
		return $division;
	} // function get_by_path()

	public static function get_one_by_name_and_parent($name, $parent)
	{
		if ($parent) {
			$id_path = 'CONCAT("' . $parent->id_path . '", id, "/")';
		} else {
			$id_path = 'CONCAT(id, "/")';
		}
		$query = DB::select()
			->from(self::$_table_name)
			->and_where_open()
			->and_where_open()
			->where('name', '=', $name['place'])
			->where('suffix', '=', $name['suffix'])
			->where('show_suffix', '=', true)
			->and_where_close()
			->or_where_open()
			->where('name', '=', $name['place'].$name['suffix'])
			->where('show_suffix', '=', false)
			->or_where_close()
			->and_where_close()
			->where('deleted_at', '=', null);
		$query->where('id_path', '=', DB::expr($id_path));
		if (isset($name['identifier']))
		{
			$query->where('identifier', '=', $name['identifier']);
		}

		$result = $query->as_object('Model_Division')->execute();
		if ($result->count())
		{
			return $result[0];
		}
		else {
			return null;
		}
	} // function get_one_by_name_and_parent()

	public static function get_top_level()
	{

		$query = DB::select()
			->from(self::$_table_name)
			->where('deleted_at', '=', null)
			->where('id_path', '=', DB::expr('CONCAT(id, "/")'))
			->order_by('display_order', 'asc');

		return $query->as_object('Model_Division')->execute();
	} // function get_top_level

	public function get_tree($date)
	{
		$divisions = Model_Division::get_by_parent_division_and_date($this, $date);
		$tree = new Model_Division_Tree($this);
		return $tree->make_tree($divisions);
	}

	public static function get_by_parent_division_and_date($parent, $date = null)
	{
		$query = DB::select('d.*')
			->from([self::$_table_name, 'd'])
			->join(['events', 's'], 'LEFT OUTER')
			->on('d.start_event_id', '=', 's.id')
			->join(['events', 'e'], 'LEFT OUTER')
			->on('d.end_event_id', '=', 'e.id')
			->and_where_open()
			->where('d.id_path', 'LIKE', DB::expr('CONCAT("' . $parent->id_path . '", "_%")'))
			->or_where('d.belongs_division_id', '=', $parent->id)
			->and_where_close()
			->where('d.deleted_at', '=', null);
		if ($date)
		{
			$query->and_where_open()
				->where('s.date', '<=', $date)
				->or_where('s.date', '=', null)
				->and_where_close()
				->and_where_open()
				->where('e.date', '>', $date)
				->or_where('e.date', '=', null)
				->and_where_close();
		}
		$query
			->order_by(DB::expr('LENGTH(d.id_path)'), 'asc')
			->order_by('d.is_empty_government_code', 'asc')
			->order_by('d.government_code', 'asc')
			->order_by('d.is_empty_kana', 'asc')
			->order_by('d.name_kana', 'asc')
			->order_by('d.end_date', 'desc');

		return $query->as_object('Model_Division')->execute();
	} // function get_by_parent_division_and_date()

	public static function get_by_admin_filter($parent, $filter)
	{
		$query = DB::select()
			->from([self::$_table_name, 'd'])
			->where('d.deleted_at', null);
		if ($parent) {
			$query->where('d.id_path', 'LIKE', DB::expr('CONCAT("' . $parent->id_path . '", "_%")'));
		} else {
			$query->where('id_path', '=', DB::expr('CONCAT(id, "/")'));
		}

		switch ($filter)
		{
			case 'empty_kana':
				$query
				/*
					->and_where_open()
					->where('d.name_kana', null)
					->or_where('d.name_kana', '')
					->or_where('d.suffix_kana', null)
					->or_where('d.suffix_kana', '')
					->and_where_close();
				*/
					->where('d.is_empty_kana', 1);
			break;

			case 'empty_code':
				$query
					->join(['events', 's'], 'LEFT OUTER')
					->on('d.start_event_id', '=', 's.id')

					->where('d.suffix', '!=', '郡')

					->and_where_open()
					->where('s.date', '>=', '1970-04-01')
					->and_where_close()

					->and_where_open()
					->where('d.government_code', null)
					->or_where('d.government_code', '')
					->and_where_close();
			break;

			case 'empty_source':
				$query
					->and_where_open()
					->where('d.source', null)
					->or_where('d.source', '')
					->and_where_close();
			break;

			case 'is_wikipedia':
				$query
					->where( DB::expr('LOWER(d.source)'), 'LIKE', 'wikipedia');
			break;
		}

		$query
			->order_by('d.display_order', 'asc')
			->order_by('d.is_empty_government_code', 'asc')
			->order_by('d.government_code', 'asc')
			->order_by('d.is_empty_kana', 'asc')
			->order_by('d.name_kana', 'asc')
			->order_by('d.end_date', 'desc');

		return $query->as_object('Model_Division')->execute();
	} // functiob get_by_admin_filter()

	public function get_path()
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

	public function make_path()
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

	public function make_path_kana()
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

	public function get_fullname_kana()
	{
		$kana = $this->name_kana;
		if ($this->show_suffix)
		{
			$kana .= '・'.$this->suffix_kana;
		}
		return $kana;
	} // function get_fullname_kana()

	public function get_search_fullname()
	{
		$name = $this->name;
		if ($this->show_suffix)
		{
			$name .= $this->suffix;
		}
		return $name;
	} // function get_search_fullname()

	public function get_search_fullname_kana()
	{
		$kana = $this->name_kana;
		if ($this->show_suffix)
		{
			$kana .= $this->suffix_kana;
		}
		return $kana;
	} // function get_search_fullname_kana()

	public function make_search_path()
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

	public function make_search_path_kana()
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

	public function get_parent_id()
	{
		$id_arr = explode('/', $this->id_path);

		// trim last empty element
		array_pop($id_arr);

		array_pop($id_arr);
		return (int)array_pop($id_arr);
	} // function get_parent_id()

	public function get_parent_path()
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

	public function get_fullname()
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
			$this->path             = '';
			$this->id_path          = '';
			$this->search_path      = '';
			$this->search_path_kana = '';
			$this->fullname         = $this->get_fullname();
			$this->save();

			$path = $parent . '/' . $this->get_fullname();
			$this->id_path          = self::make_id_path($path, $this->id);
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
} //