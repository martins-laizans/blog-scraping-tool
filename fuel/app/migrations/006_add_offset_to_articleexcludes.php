<?php

namespace Fuel\Migrations;

class Add_offset_to_articleexcludes
{
	public function up()
	{
		\DBUtil::add_fields('articleexcludes', array(
			'offset' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('articleexcludes', array(
			'offset'

		));
	}
}