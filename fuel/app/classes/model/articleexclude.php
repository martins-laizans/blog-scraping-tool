<?php
use Orm\Model;

class Model_Articleexclude extends Model
{
	protected static $_properties = array(
		'id',
		'blogid',
		'ruleid',
		'element',
                'offset',
                'length'
	);


	public static function validate($factory)
	{
		$val = Validation::forge($factory);
//		$val->add_field('blogid', 'Blogid', 'required|valid_string[numeric]');
		$val->add_field('ruleid', 'Ruleid', 'required|valid_string[numeric]');
		$val->add_field('element', 'Element', 'required|max_length[100]');

		return $val;
	}
        
        public static function get_article_wrapper($article_url,$blog_id)
        {
            Package::load('simplehtml');
            $simple_html = new Simplehtml;
            $html = $simple_html->file_get_html($article_url);
            
            // detect article wrapper
            $post = $html->find('.post');
            $post = reset($post);
            if($post)
                return $post;
            else
            {
                echo 'post not found';
                // try different search
                $post = $html->getElementByTagName('article');
                if($post)
                    return $post;
                
                $post = $html->find('.Post');
                $post = reset($post);
                if($post)
                    return $post;
                
                $post = $html->find('.ieraksts');
                $post = reset($post);
                if($post)
                    return $post;
                
                $post = $html->find('.entry');
                $post = reset($post);
                if($post)
                    return $post;

                return null;
            }
        }
        
        public static function get_article_comments($article_url,$blog_id)
        {
            Package::load('simplehtml');
            $simple_html = new Simplehtml;
            $html = $simple_html->file_get_html($article_url);
            
            // detect article wrapper
            $comment = $html->find('.comment');
            $comment = reset($comment);
            
            if($comment)
                return $comment;
            else
            {
                $comment = $html->find('.comments');
                if(key_exists(1, $comment))
                {
                    $comment = $comment[1];
                    if($comment)
                        return $comment;
                }
                return null;
            }
        }
        
        public static function get_article_content_element($article_url,$blog_id)
        {
            Package::load('simplehtml');
            $simple_html = new Simplehtml;
            $html = $simple_html->file_get_html($article_url);
            
            $content_element = Model_Articleexclude::find('all', array('where' => array(
                    array('blogid', $blog_id),
                    array('ruleid','in', array(5,6),)
            )));
            if(!$content_element)
                return 'article';
            $content_element = reset($content_element);
            if($content_element->ruleid == 6)
                $content_element = '.'.$content_element->element;
            else
                $content_element = '#'.$content_element->element;            
            return $content_element;
        }
        
        
        
        
        public static function rules()
        {
            return array(   1 => 'remove id',
                            2 => 'remove class',
                            3 => 'author',
                            4 => 'published',
                            5 => 'content id',
                            6 => 'content class',
                            7 => 'comment_author',
                            8 => 'comment_content',
                );
        }
        
        public static function get_article_author_element($blog_id)
        {
             $result = Model_Articleexclude::find('all', array('where' => array(
                    array('blogid', $blog_id),
                    array('ruleid', 3),)
            ));
            $result = reset($result);
            return ($result)? $result : null;
        }
        
        public static function get_published_element($blog_id)
        {
            $result = Model_Articleexclude::find('all', array('where' => array(
                    array('blogid', $blog_id),
                    array('ruleid', 4),)
            ));
            $result = reset($result);
            return ($result)? $result : null;
        }
        
        public static function get_comments_author_element($blog_id)
        {
            $result = Model_Articleexclude::find('all', array('where' => array(
                    array('blogid', $blog_id),
                    array('ruleid', 7),)
            ));
            $result = reset($result);
            return ($result)? $result->element : null;
        }
        
        public static function get_comments_content_element($blog_id)
        {
            $result = Model_Articleexclude::find('all', array('where' => array(
                    array('blogid', $blog_id),
                    array('ruleid', 8),)
            ));
            $result = reset($result);
            return ($result)? $result->element : null;
        }
        
        
        public static function filtered_ids($blog_id)
        {
            $result = Model_Articleexclude::find('all', array('where' => array(
                    array('blogid', $blog_id),
                    array('ruleid', 1),)
                ));
            $response = [];
            foreach ($result as $one):
                $response[$one['element']]= 1;
            endforeach;
            return $response;
        }
        
        public static function filtered_classes($blog_id)
        {
            $result = Model_Articleexclude::find('all', array('where' => array(
                    array('blogid', $blog_id),
                    array('ruleid', 2),)
                ));
            $response = [];
            foreach ($result as $one):
                $response[$one['element']]= 1;
            endforeach;
            return $response;
        }
                        
                        
}
