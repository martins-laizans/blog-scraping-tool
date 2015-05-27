<?php

namespace Fuel\Migrations;

class Create_blogs
{
	public function up()
	{
		\DBUtil::create_table('blogs', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
			'url' => array('constraint' => 255, 'type' => 'varchar'),
			'status' => array('constraint' => 11, 'type' => 'int'),
			'crawldate' => array('type' => 'date'),
			'created_at' => array('constraint' => 11, 'type' => 'int'),
			'updated_at' => array('constraint' => 11, 'type' => 'int'),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('blogs');
	}
}