<?php

/**
 * @package  App\Abstracts
 */

namespace MyApp\Abstracts;

/**
 * Orm を継承したアクティブレコードの基底クラス
 */
abstract class ActiveRecord extends \Orm\Model_Soft
{
	/**
	 * テーブル名
	 * @var string
	 */
	protected static $_table_name	= '';

	/**
	 * 主キーのカラム名
	 * @var array<int, string>
	 */
	protected static $_primary_key	= ['id'];

	/**
	 * 作成日時のカラム名
	 * @var string
	 */
	protected static $_created_at  = 'created_at';

	/**
	 * 更新日時のカラム名
	 * @var string
	 */
	protected static $_updated_at  = 'updated_at';

	/**
	 * 作成・更新日時をタイムスタンプにする
	 * @var bool
	 */
	protected static $_mysql_timestamp = true;

	/**
	 * 削除日時のカラム設定（カラム名、タイムスタンプ）
	 * @var array<string, mixed>
	 */
	protected static $_soft_delete = array(
		'deleted_field' => 'deleted_at',
		'mysql_timestamp' => true,
	);

	/**
	 * 作成・更新時にタイムスタンプを追加
	 * @var array<string, array>
	 */
	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => true,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => true,
		),
	);
}
// class ActiveRecord
