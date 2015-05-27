<?php

namespace Fuel\Migrations;

class Create_articles
{
	public function up()
	{
		\DBUtil::create_table('articles', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
			'blogid' => array('constraint' => 11, 'type' => 'int'),
			'author' => array('constraint' => 100, 'type' => 'varchar', 'null' => true),
			'url' => array('constraint' => 255, 'type' => 'varchar'),
			'published' => array('type' => 'date', 'null' => true),
			'crawled' => array('type' => 'date', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('articles');
	}
}