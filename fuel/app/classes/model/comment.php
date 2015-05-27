<?php

class Model_Comment extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'text',
		'articleid',
		'author'
	);
        
        public static function remove_dublicate($comment)
        {
            $dublicates = Model_comment::find('all', array('where' => array(
                    array('text', $comment->text),
                    array('articleid',$comment->articleid),
                    array('id', '!=', $comment->id),
            )));
            $count = 0;
            foreach ($dublicates as $duplicate):
                $duplicate->delete();
                $count++;
            endforeach;
            echo $count.' deleted <br>';
        }

}
