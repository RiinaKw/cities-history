<?php

namespace Fuel\Migrations;

class Create_admins
{
	public function up()
	{
		\DBUtil::create_table('admins', array(
			'id'                        => array('type' => 'int', 'unsigned' => true, 'null' => false, 'auto_increment' => true, 'constraint' => 11),
			'login_id'                  => array('constraint' => 100, 'null' => false, 'type' => 'varchar'),
			'password_crypt'            => array('constraint' => 256, 'null' => false, 'type' => 'varchar'),
			'remember_me_hash'          => array('constraint' => 256, 'null' => true, 'type' => 'varchar'),
			'created_at' => array( 'type' => 'timestamp', 'null' => true),
			'updated_at' => array( 'type' => 'timestamp', 'null' => true),
			'deleted_at' => array( 'type' => 'timestamp', 'null' => true),
		), array('id'));
		\DBUtil::create_index('admins', 'login_id', 'uq_users_login_id', 'unique');
	}

	public function down()
	{
		\DBUtil::drop_table('users');
	}
}
