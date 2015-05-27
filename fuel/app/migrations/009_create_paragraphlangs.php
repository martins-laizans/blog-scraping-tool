<?php

namespace Fuel\Migrations;

class Create_paragraphlangs
{
	public function up()
	{
		\DBUtil::create_table('paragraphlangs', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
			'paragraphid' => array('constraint' => 11, 'type' => 'int'),
			'language' => array('constraint' => 2, 'type' => 'char'),
			'probability' => array('type' => 'float'),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('paragraphlangs');
	}
}