<?php

class Model_Paragraph extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'text',
		'articleid',
		'symbols',
		'spaces'
	);
        
         public static function remove_dublicate($paragraph)
        {
            $dublicates = Model_Paragraph::find('all', array('where' => array(
                    array('articleid', $paragraph->articleid),
                    array('symbols',$paragraph->symbols),
                    array('spaces', $paragraph->spaces ),
                    array('id', '!=', $paragraph->id),
            )));
            $count = 0;
            foreach ($dublicates as $duplicate):
                $duplicate->delete();
                $count++;
            endforeach;
            echo $count.' deleted <br>';
                
        }
        
        public static function get_total_count()
        {
            return Model_Paragraph::query()->count();
        }
        public static function get_parsed_spaces_count()
        {
            $result = DB::query('select SUM(spaces) from paragraphs')->execute();
            return ($result[0]['SUM(spaces)']);
        }


}
