<?php

/**
 * @package  App\Model
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @todo PHPMD をなんとかしろ
 */
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

	public function validation()
	{
		$validation = Validation::forge(mt_rand());

		// rules
		$validation->add('name', '自治体名')
			->add_rule('required')
			->add_rule('max_length', 20);
		$validation->add('name_kana', '自治体名かな')
			->add_rule('required')
			->add_rule('max_length', 20);
		$validation->add('suffix', '自治体名種別')
			->add_rule('required')
			->add_rule('max_length', 20);
		$validation->add('suffix_kana', '自治体名種別かな')
			->add_rule('required')
			->add_rule('max_length', 20);
		$validation->add('identifier', '識別名')
			->add_rule('max_length', 50);
		$validation->add('start_event_id', '設置イベント');
		$validation->add('end_event_id', '廃止イベント');
		$validation->add('government_code', '全国地方公共団体コード')
			->add_rule('min_length', 6)
			->add_rule('max_length', 7);
		$validation->add('display_order', '表示順')
			->add_rule('valid_string', array('numeric'));
		$validation->add('source', '出典');

		return $validation;
	}
	// function validation()

	public function get_source(): string
	{
		return Helper_Html::wiki($this->source);
	}
	// function get_source()

	public function get_tree($date): Model_Division_Tree
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
	}
	// function get_path()

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
	}
	// function make_path()

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
	}
	// function make_path_kana()

	public function get_fullname_kana(): string
	{
		$kana = $this->name_kana;
		if ($this->show_suffix) {
			$kana .= '・' . $this->suffix_kana;
		}
		return $kana;
	}
	// function get_fullname_kana()

	public function get_search_fullname(): string
	{
		$name = $this->name;
		if ($this->show_suffix) {
			$name .= $this->suffix;
		}
		return $name;
	}
	// function get_search_fullname()

	public function get_search_fullname_kana(): string
	{
		$kana = $this->name_kana;
		if ($this->show_suffix) {
			$kana .= $this->suffix_kana;
		}
		return $kana;
	}
	// function get_search_fullname_kana()

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
	}
	// function make_path()

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
	}
	// function make_path_kana()

	public function get_parent_path(): ?string
	{
		$path = $this->get_path();
		if (strpos($path, '/') === false) {
			return null;
		} else {
			return dirname($path);
		}
	}
	// function get_parent_path()

	public function get_belongs_path(): ?string
	{
		if ($this->belongs_division_id) {
			$division = self::find_by_pk($this->belongs_division_id);
			return $division->get_path();
		} else {
			return null;
		}
	}
	// function get_belongs_path()

	public function get_belongs_name(): ?string
	{
		if ($this->belongs_division_id) {
			$division = self::find_by_pk($this->belongs_division_id);
			return $division->get_fullname();
		} else {
			return null;
		}
	}
	// function get_belongs_name()

	public function get_fullname(): string
	{
		$name = $this->name;
		if ($this->show_suffix) {
			$name .= $this->suffix;
		}
		if ($this->identifier) {
			$name .= "({$this->identifier})";
		}
		return $name;
	}
	// function get_fullname()

	public function suffix_classification(): string
	{
		switch ($this->suffix) {
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

	/**
	 * 必要なパラメータが設定されている場合のみコールバックを実行
	 *
	 * @param array&<string, mixed>   $array     対象の配列
	 * @param string                  $key       対象のキー
	 * @param callable                $callback  実行するコールバック
	 */
	protected function callIfNotEmpty(array &$array, string $key, callable $callback): void
	{
		if (isset($array[$key])) {
			$callback($this, $array[$key]);
		}
	}

	/**
 	 * @SuppressWarnings(PHPMD.ExitExpression)
 	 * @todo PHPMD をなんとかしろ
	 */
	public function create($input)
	{
		$belongs = $input['belongs'] ?? null;
		$parent = $input['parent'] ?? null;

		try {
			DB::start_transaction();

			if ($belongs) {
				$belongs_division = Table_Division::get_by_path($belongs);
				if (! $belongs_division) {
					$belongs_division = Table_Division::set_path($belongs);
					$belongs_division = array_pop($belongs_division);
				}
				$this->belongs_division_id = $belongs_division->id;
			} else {
				$this->belongs_division_id = null;
			}

			$this->name = $input['name'] ?? null;

			$this->callIfNotEmpty($input, 'name_kana', function ($obj, $value) {
				$obj->name_kana       = Helper_String::to_hiragana($value);
				$obj->is_empty_kana   = empty($value);
			});

			$this->suffix = $input['suffix'] ?? null;
			$this->suffix_kana = $input['suffix_kana'] ? Helper_String::to_hiragana($input['suffix_kana']) : null;
			$this->show_suffix = $input['show_suffix'] ? (bool)$input['show_suffix'] : false;

			$this->callIfNotEmpty($input, 'government_code', function ($obj, $value) {
				$obj->government_code = Helper_Governmentcode::normalize($value);
				$obj->is_empty_government_code = empty($value);
			});

			$this->display_order = $input['display_order'] ?? null;
			$this->is_unfinished = $input['is_unfinished'] ? (bool)$input['is_unfinished'] : false;
			$this->identifier = $input['identifier'] ?? null;
			$this->source = $input['source'] ?? null;

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
			if ($query->execute()->count()) {
				throw new HttpBadRequestException('重複しています。');
			}
			$this->save();

			DB::commit_transaction();
		} catch (HttpBadRequestException $e) {
			// internal error
			DB::rollback_transaction();
			throw new HttpBadRequestException($e->getMessage());
		} catch (Exception $e) {
			Debug::dump($e, $e->getTraceAsString());
			exit;
			// internal error
			DB::rollback_transaction();
			throw new HttpServerErrorException($e->getMessage());
		}
	}
	// function create()

	public function dump(): void
	{
		echo $this->id_path, ' ', $this->path;
	}
}
// class Model_Division
