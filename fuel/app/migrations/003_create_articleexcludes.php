<?php

namespace Fuel\Migrations;

class Create_articleexcludes
{
	public function up()
	{
		\DBUtil::create_table('articleexcludes', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
			'blogid' => array('constraint' => 11, 'type' => 'int'),
			'ruleid' => array('constraint' => 11, 'type' => 'int'),
			'element' => array('constraint' => 100, 'type' => 'varchar'),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('articleexcludes');
	}
}