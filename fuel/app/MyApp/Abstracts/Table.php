<?php

namespace MyApp\Abstracts;

/**
 * データベースのテーブル操作に特化した基底クラス
 * （ActiveRecord 機能は一切持たない）
 *
 * @package  App\Abstracts
 */
abstract class Table
{
	/**
	 * 管理するテーブル名
	 * @var string
	 */
	public const TABLE_NAME = '';

	/**
	 * 管理するテーブルの主キー
	 * @var array<int, string>
	 */
	public const TABLE_PK = [];
}
