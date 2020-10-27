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

	public static function get_all()
	{
		$query = DB::select()
			->from(self::$_table_name)
			->where('deleted_at', '=', null)
			->order_by('name_kana', 'ASC');

		return $query->as_object('Model_Division')->execute()->as_array();
	} // function get_all()

	public static function get_all_id()
	{
		$query = DB::select('id')
			->from(self::$_table_name)
			->where('deleted_at', '=', null)
			->order_by('name_kana', 'ASC');

		$result = $query->execute()->as_array();
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

		return $query->as_object('Model_Division')->execute()->as_array();
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
				->where('path', 'LIKE', '%'.$word.'%')
				->or_where('path_kana', 'LIKE', '%'.$word.'%')
				->and_where_close();
		}
		$query
			->order_by('is_empty_government_code', 'asc')
			->order_by('government_code', 'asc')
			->order_by('is_empty_kana', 'asc')
			->order_by('name_kana', 'asc')
			->order_by('end_date', 'desc');

		return $query->as_object('Model_Division')->execute()->as_array();
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
					'fullname_kana' => '',
					'path' => '',
					'path_kana' => '',
					'show_suffix' => true,
					'identifier' => (isset($matches['identifier']) ? $matches['identifier'] : null),
					'is_unfinished' => true,
					'is_empty_government_code' => true,
					'is_empty_kana' => true,
					'end_date' => '9999-12-31',
					'source' => '',
				]);

				$division->save();

				$division->id_path = self::make_id_path($path, $division->id);
				$division->fullname = $division->get_fullname();
				$division->path = $division->get_path();

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

			$division->save();

			$division->fullname      = $division->get_fullname();
			$division->fullname_kana = $division->get_fullname_kana();
			$division->path          = $division->make_path();
			$division->path_kana     = $division->make_path_kana();

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

		$result = $query->as_object('Model_Division')->execute()->as_array();
		if ($result)
		{
			return $result[0];
		}
		else {
			return null;
		}
	} // function get_one_by_name_and_parent()

	public function get_parents_and_self()
	{
		$query = DB::select()
			->from(self::$_table_name)
			->where('deleted_at', '=', null);
		$query->where(DB::expr('"' . $this->id_path . '"'), 'LIKE', DB::expr('CONCAT(id_path, "%")'));
		$query->order_by(DB::expr('LENGTH(path)', 'ASC'));

		return $query->as_object('Model_Division')->execute()->as_array();
	} // function get_parents_and_self()

	public static function get_top_level()
	{

		$query = DB::select()
			->from(self::$_table_name)
			->where('deleted_at', '=', null)
			->where('id_path', '=', DB::expr('CONCAT(id, "/")'))
			->order_by('display_order', 'asc');

		return $query->as_object('Model_Division')->execute()->as_array();
	} // function get_top_level()

	public function get_tree($date)
	{
		$divisions = Model_Division::get_by_parent_division_and_date($this, $date);

		// count divisions by suffix
		$count = [
			'支庁' => 0,
			'総合振興局' => 0,
			'振興局' => 0,
			'市' => 0,
			'区' => 0,
			'郡' => 0,
			'町' => 0,
			'村' => 0,
		];
		$child_divisions = [];
		foreach ($divisions as $division)
		{
			$child_divisions[$division->id] = $division;

			if ( ! isset($count[$division->suffix]))
			{
				$count[$division->suffix] = 0;
			}
			$count[$division->suffix]++;
		}

		// create tree
		$ids_tree = [];
		foreach ($child_divisions as $child)
		{
			$parent_ids = [$child->get_parent_id(), $child->belongs_division_id];
			foreach ($parent_ids as $parent_id)
			{
				if ($parent_id)
				{
					if ( ! isset($ids_tree[$parent_id]))
					{
						$ids_tree[$parent_id] = [
							'count' => [
								'区' => 0,
								'町' => 0,
								'村' => 0,
							],
							'children' => [],
						];
					}
					if ( ! isset($ids_tree[$parent_id]['count'][$child->suffix]))
					{
						$ids_tree[$parent_id]['count'][$child->suffix] = 0;
					}
					$ids_tree[$parent_id]['count'][$child->suffix]++;
					$ids_tree[$parent_id]['children'][$child->id] = $child->id;
				}
			}
		}
		if ($ids_tree)
		{
			foreach ($ids_tree[$this->id]['children'] as $id)
			{
				if (isset($ids_tree[$id]))
				{
					$tree = $ids_tree[$id];
					$ids_tree[$this->id]['children'][$id] = $tree;
					unset($ids_tree[$id]);
				}
			}
		}

		$divisions_tree = [
			'支庁' => [],
			'区' => [],
			'市' => [],
			'郡' => [],
			'町村' => [],
		];
		if ($ids_tree)
		{
			foreach ($ids_tree[$this->id]['children'] as $id => $child)
			{
				$div = $child_divisions[$id];
				$suffix = $div->suffix;
				switch ($suffix)
				{
					case '区':
					case '市':
					case '郡':
					break;

					case '支庁':
					case '総合振興局':
					case '振興局':
						$suffix = '支庁';
					break;

					default:
						$suffix = '町村';
					break;
				} // swtich
				if (is_array($child))
				{
					$div->_count = $child['count'];
					$divisions_tree[$suffix][$id] = $div;
					foreach ($child['children'] as $town_id)
					{
						$town = $child_divisions[$town_id];
						$town_suffix = $town->suffix;
						switch ($town_suffix)
						{
							case '区':
							break;

							default:
								$town_suffix = '町村';
							break;
						} // swtich
						if ( ! isset($divisions_tree[$suffix][$id]->_children[$town_suffix]))
						{
							$divisions_tree[$suffix][$id]->_children[$town_suffix] = [];
						}
						$divisions_tree[$suffix][$id]->_children[$town_suffix][$town_id] = $town;
					} // foreach
				}
				else
				{
					$divisions_tree[$suffix][$id] = $div;
				}
			} // foreach
		}

		return [
			'count' => $count,
			'tree' => $divisions_tree,
		];
	} // function get_tree()

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
			->order_by('d.is_empty_government_code', 'asc')
			->order_by('d.government_code', 'asc')
			->order_by('d.is_empty_kana', 'asc')
			->order_by('d.name_kana', 'asc')
			->order_by('d.end_date', 'desc');

		return $query->as_object('Model_Division')->execute()->as_array();
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

		return $query->as_object('Model_Division')->execute()->as_array();
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

	public function get_parent_id()
	{
		$id_arr = explode('/', $this->id_path);

		// trim last empty element
		array_pop($id_arr);

		array_pop($id_arr);
		return (int)array_pop($id_arr);
	}

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
				$this->name_kana       = $input['name_kana'];
				$this->is_empty_kana   = empty($input['name_kana']);
			}
			if (isset($input['suffix'])) {
				$this->suffix          = $input['suffix'];
			}
			if (isset($input['suffix_kana'])) {
				$this->suffix_kana     = $input['suffix_kana'];
			}
			if (isset($input['show_suffix'])) {
				$this->show_suffix     = !! $input['show_suffix'];
			}
			if (isset($input['government_code'])) {
				$this->government_code     = $input['government_code'];
				$this->is_empty_government_code = empty($input['government_code']);
			}
			$this->identifier      = $input['identifier'] ?? null;
			$this->government_code = $input['government_code'] ?? null;
			if (isset($input['display_order']))
			{
				$this->display_order = empty($input ['display_order']) ? null : $input['display_order'];
			}
			$this->fullname        = '';
			$this->fullname_kana   = '';
			$this->path            = '';
			$this->path_kana       = '';
			$this->is_unfinished   = isset($input['is_unfinished']) && ! $input['is_unfinished'] ? false : true;
			$this->source          = $input['source'] ?? null;
			$this->save();

			$path = $parent . '/' . $this->get_fullname();
			$this->id_path = self::make_id_path($path, $this->id);

			$this->fullname      = $this->get_fullname();
			$this->fullname_kana = $this->get_fullname_kana();
			$this->path          = $this->make_path();
			$this->path_kana     = $this->make_path_kana();

			$query = DB::select()
				->from(self::$_table_name)
				->where('deleted_at', '=', null)
				->where('path', '=', $this->path)
				->where('id', '!=', $this->id)
				;
			if ($query->execute()->as_array())
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
} // class Model_Division
