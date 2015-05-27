<?php

namespace Fuel\Migrations;

class Create_archives
{
	public function up()
	{
		\DBUtil::create_table('archives', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
			'blogid' => array('constraint' => 11, 'type' => 'int'),
			'elementid' => array('constraint' => 11, 'type' => 'int'),
			'element' => array('constraint' => 100, 'type' => 'varchar'),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('archives');
	}
}