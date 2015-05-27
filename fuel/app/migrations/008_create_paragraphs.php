<?php

namespace Fuel\Migrations;

class Create_paragraphs
{
	public function up()
	{
		\DBUtil::create_table('paragraphs', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
			'text' => array('constraint' => 2000, 'type' => 'varchar'),
			'articleid' => array('constraint' => 11, 'type' => 'int'),
			'symbols' => array('constraint' => 11, 'type' => 'int'),
			'spaces' => array('constraint' => 11, 'type' => 'int'),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('paragraphs');
	}
}