<?php

class Model_Division extends Model_Base
{
	const RE_SUFFIX = '/^(?<place>.+?)(?<suffix>都|府|県|支庁|市|郡|区|町|村|郷|城下|駅|宿|新宿|組|新田|新地)(\((?<identifier>.+?)\))?$/';

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
		$field = $validation->add('parent_division_id', '親自治体');
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
			->where('deleted_at', '=', null)
			->order_by('name_kana', 'ASC');
		foreach ($q_arr as $word)
		{
			$query->and_where_open()
				->where('fullname', 'LIKE', '%'.$word.'%')
				->or_where('fullname_kana', 'LIKE', '%'.$word.'%')
				->and_where_close();
		}

		return $query->as_object('Model_Division')->execute()->as_array();
	} // function search()

	public static function set_path($path)
	{
		$arr = explode('/', $path);
		$parent_id = null;
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
			if ( ! $division = self::get_one_by_name_and_parent_id($matches, $parent_id))
			{
				$division = self::forge([
					'name' => $matches['place'],
					'name_kana' => '',
					'suffix' => $matches['suffix'],
					'suffix_kana' => '',
					'fullname' => '',
					'fullname_kana' => '',
					'show_suffix' => true,
					'identifier' => (isset($matches['identifier']) ? $matches['identifier'] : null),
					'parent_division_id' => $parent_id,
					'is_unfinished' => true,
					'is_empty_government_code' => true,
					'is_empty_kana' => true,
					'end_date' => '9999-12-31',
					'source' => '',
				]);
				$division->fullname = $division->get_path(null, true);

				if ($parent_id) {
					$parent = $division;
					while ($parent->parent_division_id !== null)
					{
						$parent = Model_Division::find_by_pk($parent->parent_division_id);
					}
					$division->top_parent_division_id = $parent->id;
				}

				$division->save();

				Model_Activity::insert_log([
					'user_id' => Session::get('user_id'),
					'target' => 'add division',
					'target_id' => $division->id,
				]);
			}
			$divisions[] = $division;
			$parent_id = $division->id;
		}
		return $divisions;
	} // function set_path()

	public static function get_by_path($path, $parent_id = null)
	{
		$arr = explode('/', $path);
		$division = null;
		$parent_id = null;
		foreach ($arr as $name)
		{
			preg_match(static::RE_SUFFIX, $name, $matches);

			if ($matches)
			{
				$result = self::get_one_by_name_and_parent_id($matches, $parent_id);
				if ($result)
				{
					$division = $result;
					$parent_id = $division->id;
				}
				else
				{
					$division = null;
					$parent_id = null;
					break;
				}
			}
			else
			{
				$matches = array(
					'place' => $name,
					'suffix' => '',
				);
				$result = self::get_one_by_name_and_parent_id($matches, $parent_id);
				if ($result)
				{
					$division = $result;
					$parent_id = $division->id;
				}
				else
				{
					$division = null;
					$parent_id = null;
					break;
				}
			}
		}
		return $division;
	} // function get_by_path()

	public static function get_one_by_name_and_parent_id($name, $parent_id)
	{
		$query = DB::select()
			->from(self::$_table_name)
			->where('parent_division_id', '=', $parent_id)
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
	} // function _get_one_by_name_and_parent_id()

	public static function get_top_level()
	{
		$query = DB::select()
			->from(self::$_table_name)
			->where('deleted_at', '=', null)
			->where('parent_division_id', '=', null)
			->order_by('display_order', 'asc');

		return $query->as_object('Model_Division')->execute()->as_array();
	} // function get_top_level()

	public static function get_by_suffix_and_date($parent_id, $suffix, $date = null)
	{
		$query = DB::select('d.*')
			->from([self::$_table_name, 'd'])
			->join(['events', 's'], 'LEFT OUTER')
			->on('d.start_event_id', '=', 's.id')
			->join(['events', 'e'], 'LEFT OUTER')
			->on('d.end_event_id', '=', 'e.id')
			->where('d.deleted_at', '=', null)
			->where('d.suffix', '=', $suffix)
			->where('d.parent_division_id', '=', $parent_id);
		if ($date)
		{
			$query->and_where_open()
				->where('s.date', '<=', $date)
				->or_where('s.date', '=', null)
				->and_where_close()
				->and_where_open()
				->where('e.date', '>=', $date)
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
	} // function get_by_suffix_and_date()

	public function get_tree($date)
	{
		$divisions = [];
		if ($this->top_parent_division_id)
		{
			$ids = Model_Division::get_by_parent_division_id_and_date($this->id, $date);
			foreach ($ids as $id)
			{
				$divisions[] = Model_Division::find_by_pk($id);
			}
		}
		else
		{
			$divisions = Model_Division::get_by_top_parent_division_id_and_date($this->id, $date);
		}

		// count divisions by suffix
		$count = [
			'支庁' => 0,
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
			$parent_ids = [$child->parent_division_id, $child->belongs_division_id];
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
					case '支庁':
					case '区':
					case '市':
					case '郡':
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

	public static function get_by_top_parent_division_id_and_date($id, $date = null)
	{
		$query = DB::select('d.*')
			->from([self::$_table_name, 'd'])
			->join(['events', 's'], 'LEFT OUTER')
			->on('d.start_event_id', '=', 's.id')
			->join(['events', 'e'], 'LEFT OUTER')
			->on('d.end_event_id', '=', 'e.id');
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
			->where('d.top_parent_division_id', '=', $id)
			->where('d.deleted_at', '=', null);
		$query
			->order_by('d.is_empty_government_code', 'asc')
			->order_by('d.government_code', 'asc')
			->order_by('d.is_empty_kana', 'asc')
			->order_by('d.name_kana', 'asc')
			->order_by('d.end_date', 'desc');

		return $query->as_object('Model_Division')->execute()->as_array();
	} // function get_by_top_parent_division_id_and_date()

	public static function get_by_date($date = null, $parent_id = null)
	{
		$query = DB::select('d.*')
			->from([self::$_table_name, 'd'])
			->join(['events', 's'], 'LEFT OUTER')
			->on('d.start_event_id', '=', 's.id')
			->join(['events', 'e'], 'LEFT OUTER')
			->on('d.end_event_id', '=', 'e.id');
		if ($date)
		{
			$query->and_where_open()
				->where('s.date', '<=', $date)
				->or_where('s.date', '=', null)
				->and_where_close()
				->and_where_open()
				->where('e.date', '>=', $date)
				->or_where('e.date', '=', null)
				->and_where_close();
		}
		if ($parent_id)
		{
			$query->where('d.parent_division_id', '=', $parent_id);
		}
		$query->order_by('d.name_kana', 'ASC');

		$divisions = $query->as_object('Model_Division')->execute()->as_array();
		return $divisions;
	} // function get_by_date()

	public static function get_by_parent_division_id_and_date($division_id, $date = null)
	{
		$query = DB::select('d.*')
			->from([self::$_table_name, 'd'])
			->join(['events', 's'], 'LEFT OUTER')
			->on('d.start_event_id', '=', 's.id')
			->join(['events', 'e'], 'LEFT OUTER')
			->on('d.end_event_id', '=', 'e.id')
			->and_where_open()
			->where('d.parent_division_id', '=', $division_id)
			->or_where('d.belongs_division_id', '=', $division_id)
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
			->order_by('is_empty_kana', 'asc')
			->order_by('d.name_kana', 'asc')
			->order_by('d.end_date', 'desc');

		$divisions = $query->as_object('Model_Division')->execute()->as_array();
		$d_arr = [];
		if ($divisions)
		{
			foreach ($divisions as $d)
			{
				$d_arr[] = $d->id;
				$child_arr = static::get_by_parent_division_id_and_date($d->id, $date);
				if ($child_arr)
				{
					$d_arr = array_merge($d_arr, $child_arr);
				}
			}
		}
		return array_unique($d_arr);
	} // get_by_parent_division_id_and_date

	public function get_parent()
	{
		return self::find_by_pk($this->parent_division_id);
	} // function get_parent()

	public function get_path($current, $force_fullpath = false)
	{
		if ($force_fullpath)
		{
			$division = $this;
			$path = '';
			do {
				$name = $division->name;
				if ($division->show_suffix)
				{
					$name .= $division->suffix;
				}
				if ($division->identifier)
				{
					$name .= '('.$division->identifier.')';
				}
				$path = ($path ? $name.'/'.$path : $name);
				$parent_id = $division->parent_division_id;
				$division = Model_Division::find_by_pk($parent_id);
			} while ($division);

			return $path;
		}
	} // function get_path()

	public function get_kana()
	{
		$kana = $this->name_kana;
		if ($this->show_suffix)
		{
			$kana .= '・'.$this->suffix_kana;
		}
		return $kana;
	} // function get_kana()

	public function get_parent_path()
	{
		if ($this->parent_division_id)
		{
			$path = $this->get_path(null, true);
			return dirname($path);
		}
		else
		{
			return null;
		}
	} // function get_parent_path()

	public function get_belongs_path()
	{
		if ($this->belongs_division_id)
		{
			$division = self::find_by_pk($this->belongs_division_id);
			return $division->get_path(null, true);
		}
		else
		{
			return null;
		}
	} // function get_belongs_path()

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
		$parent = $input['parent'];
		$belongs = $input['belongs'];

		try
		{
			DB::start_transaction();

			if ($parent)
			{
				$parent_division = self::get_by_path($parent);
				if ( ! $parent_division)
				{
					$parent_division = self::set_path($parent);
					$parent_division = array_pop($parent_division);
				}
				$this->parent_division_id = $parent_division->id;

				$parent = $parent_division;
				while ($parent->parent_division_id !== null)
				{
					$parent = Model_Division::find_by_pk($parent->parent_division_id);
				}
				$this->top_parent_division_id = $parent->id;
			}
			else
			{
				$this->parent_division_id = null;
			}
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

			$this->name            = $input['name'];
			$this->name_kana       = $input['name_kana'];
			$this->suffix          = $input['suffix'];
			$this->suffix_kana     = $input['suffix_kana'];
			$this->show_suffix     = isset($input['show_suffix']) && $input['show_suffix'] ? true : false;
			$this->identifier      = $input['identifier'] ?: null;
			$this->government_code = $input['government_code'] ?: null;
			$this->display_order   = $input['display_order'] ?: null;
			$this->fullname        = '';
			$this->fullname_kana   = '';
			$this->is_unfinished   = isset($input['is_unfinished']) && $input['is_unfinished'] ? true : false;
			$this->is_empty_kana   = empty($input['name_kana']);
			$this->is_empty_government_code = empty($input['government_code']);
			$this->source          = $input['source'] ?: null;
			$this->save();

			$this->fullname = $this->get_path(null, true);
			$this->path = $this->get_path(null, true);
			$this->fullname_kana = $this->name_kana.$this->suffix_kana;

			$query = DB::select()
				->from(self::$_table_name)
				->where('deleted_at', '=', null)
				->where('fullname', '=', $this->fullname)
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
