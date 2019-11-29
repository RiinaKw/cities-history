<?php

namespace Fuel\Migrations;

class Create_pages
{
	public function up()
	{
		\DBUtil::create_table('pages', array(
			'id'                 => array('type' => 'int', 'unsigned' => true, 'null' => false, 'auto_increment' => true, 'constraint' => 11),
			'slug'               => array('constraint' => 20, 'null' => false, 'type' => 'varchar'),
			'title'              => array('constraint' => 100, 'null' => false, 'type' => 'varchar'),
			'content'            => array('null' => false, 'type' => 'text'),
			'author_id'          => array('constraint' => 11,  'null' => true, 'type' => 'int'),
			'created_at'         => array( 'type' => 'timestamp', 'null' => true),
			'updated_at'         => array( 'type' => 'timestamp', 'null' => true),
			'deleted_at'         => array( 'type' => 'timestamp', 'null' => true),
		), array('id'));
		\DBUtil::create_index('pages', 'slug', 'pages_slug');
		\DBUtil::create_index('pages', 'author_id', 'pages_author_id');
		\DBUtil::create_index('pages', 'updated_at', 'pages_updated_at');
	}

	public function down()
	{
		\DBUtil::drop_table('pages');
	}
}
