<?php

class Model_Division extends Model_Base
{
	protected static $_table_name  = 'divisions';
	protected static $_primary_key = 'id';
	protected static $_created_at  = 'created_at';
	protected static $_updated_at  = 'updated_at';
	protected static $_deleted_at  = 'deleted_at';
	protected static $_mysql_timestamp = true;

	public function validation($is_new = false, $factory = null)	// 引数は単なる識別子、何でもいい
	{
		$validation = Validation::forge($factory);

		// 入力ルール
		$field = $validation->add('name', '自治体名')
			->add_rule('required')
			->add_rule('max_length', 20);
		$field = $validation->add('name_kana', '自治体名かな')
			->add_rule('required')
			->add_rule('max_length', 20);
		$field = $validation->add('postfix', '自治体名種別')
			->add_rule('required')
			->add_rule('max_length', 20);
		$field = $validation->add('postfix_kana', '自治体名種別かな')
			->add_rule('required')
			->add_rule('max_length', 20);
		$field = $validation->add('identify', '識別名')
			->add_rule('max_length', 50);
		$field = $validation->add('parent_division_id', '親自治体');
		$field = $validation->add('start_event_id',     '設置イベント');
		$field = $validation->add('end_event_id',       '廃止イベント');

		return $validation;
	} // function validation()

	public static function get_all()
	{
		$query = DB::select()
			->from(self::$_table_name)
			->where('deleted_at', '=', null);

		return $query->as_object('Model_Division')->execute()->as_array();
	} // function get_all()

	public static function search($q)
	{
		$q = str_replace(array('\\', '%', '_'), array('\\\\', '\%', '\_'), $q);
		$query = DB::select()
			->from(self::$_table_name)
			->where('deleted_at', '=', null)
			->and_where_open()
			->where(DB::expr('concat(name, postfix)'), 'LIKE', '%'.$q.'%')
			->or_where(DB::expr('concat(name_kana, postfix_kana)'), 'LIKE', '%'.$q.'%')
			->and_where_close();

		return $query->as_object('Model_Division')->execute()->as_array();
	} // function search()

	public static function set_path($path)
	{
		$arr = explode('/', $path);
		$parent_id = null;
		$divisions = [];
		foreach ($arr as $name)
		{
			preg_match('/^(?<place>.+?)(?<postfix>都|道|府|県|市|郡|区|町|村)(\((?<identify>.+?)\))?$/', $name, $matches);
			if ( ! $division = self::get_one_by_name_and_parent_id($matches, $parent_id))
			{
				$division = self::forge([
					'name' => $matches['place'],
					'name_kana' => '',
					'postfix' => $matches['postfix'],
					'postfix_kana' => '',
					'identify' => (isset($matches['identify']) ? $matches['identify'] : null),
					'parent_division_id' => $parent_id,
				]);
				$division->save();
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
			preg_match('/^(?<place>.+?)(?<postfix>都|道|府|県|市|郡|区|町|村)(\((?<identify>.+?)\))?$/', $name, $matches);
			if ($matches)
			{
				$result = self::get_one_by_name_and_parent_id($matches, $parent_id);
				if ($result)
				{
					$division = $result;
					$parent_id = $division->id;
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
			->where('name', '=', $name['place'])
			->where('postfix', '=', $name['postfix']);
		if (isset($name['identify']))
		{
			$query->where('identify', '=', $name['identify']);
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
			->where('parent_division_id', '=', null);

		return $query->as_object('Model_Division')->execute()->as_array();
	} // function get_top_level()

	public static function get_by_postfix($parent_id, $postfix)
	{
		$query = DB::select()
			->from(self::$_table_name)
			->where('postfix', '=', $postfix)
			->where('parent_division_id', '=', $parent_id);

		return $query->as_object('Model_Division')->execute()->as_array();
	} // function get_by_postfix()

	public static function get_by_parent_division_id($division_id)
	{
		$divisions = Model_Division::find_by_parent_division_id($division_id);
		$d_arr = [];
		if ($divisions)
		{
			foreach ($divisions as $d)
			{
				$d_arr[] = $d->id;
				$child_arr = static::get_by_parent_division_id($d->id);
				if ($child_arr)
				{
					$d_arr = array_merge($d_arr, $child_arr);
				}
			}
		}
		return $d_arr;
	}

	public function get_parent()
	{
		return self::find_by_pk($this->parent_division_id);
	}

	public function get_path($current, $force_fullpath = false)
	{
		if ($force_fullpath)
		{
			$path = $this->name.$this->postfix;
			if ($this->identify)
			{
				$path .= '('.$this->identify.')';
			}
			$parent_id = $this->parent_division_id;
			while ($parent_id)
			{
				$parent = Model_Division::find_by_pk($parent_id);
				$name = $parent->name.$parent->postfix;
				if ($parent->identify)
				{
					$name .= '('.$parent->identify.')';
				}
				$path = $name.'/'.$path;
				$parent_id = $parent->parent_division_id;
			}

			return $path;
		}
	} // function get_path()

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

	public function get_fullname()
	{
		$name = $this->name.$this->postfix;
		if ($this->identify)
		{
			$name .= '('.$this->identify.')';
		}
		return $name;
	}
} // class Model_Division
