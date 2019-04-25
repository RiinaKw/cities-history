<?php

namespace Fuel\Migrations;

class Rename_admins_to_users
{
	public function up()
	{
		if(\DBUtil::table_exists('admins'))
		{
			\DBUtil::rename_table('admins', 'users');
		}
	}

	public function down()
	{
		if(\DBUtil::table_exists('users'))
		{
			\DBUtil::rename_table('users', 'admins');
		}
	}
}
