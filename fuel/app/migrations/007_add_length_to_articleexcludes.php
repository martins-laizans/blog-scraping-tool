<?php

namespace Fuel\Migrations;

class Add_length_to_articleexcludes
{
	public function up()
	{
		\DBUtil::add_fields('articleexcludes', array(
			'length' => array('constraint' => 11, 'type' => 'int', 'null' => true),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('articleexcludes', array(
			'length'

		));
	}
}