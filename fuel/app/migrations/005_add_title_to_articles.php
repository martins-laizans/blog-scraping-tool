<?php

namespace Fuel\Migrations;

class Add_title_to_articles
{
	public function up()
	{
		\DBUtil::add_fields('articles', array(
			'title' => array('constraint' => 200, 'type' => 'varchar'),

		));
	}

	public function down()
	{
		\DBUtil::drop_fields('articles', array(
			'title'

		));
	}
}