<?php

namespace Fuel\Migrations;

class Create_comments
{
	public function up()
	{
		\DBUtil::create_table('comments', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
			'text' => array('constraint' => 1000, 'type' => 'varchar'),
			'articleid' => array('constraint' => 11, 'type' => 'int'),
			'author' => array('constraint' => 100, 'type' => 'varchar'),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('comments');
	}
}