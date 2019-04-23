<?php
return array(
	'_root_'  => 'list/index',  // The default route
	'_404_'   => 'welcome/404',    // The main 404 route

	'add(/:path)?'  => 'add/index',

	'division/list' => 'rest/division/list',

	'event/add'          => 'event/add',
	'event/(.+?)/edit'   => 'event/edit/$1',
	'event/(.+?)/delete' => 'event/delete/$1',
	'event/(.+?)'        => 'rest/event/detail/$1',

	'division/belongto(/:path)?' => 'division/belongto',
	'division/edit(/:path)?'     => 'division/edit',
	'division(/:path)?'          => 'division/detail',
);
