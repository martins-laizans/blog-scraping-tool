<?php
use Orm\Model;

class Model_Article extends Model
{
	protected static $_properties = array(
		'id',
		'blogid',
                'title',
		'author',
		'url',
		'published',
		'crawled',
	);

        public static function get_total_count()
        {
            return Model_Article::query()->count();
        }
        public static function get_parsed_count()
        {
            return Model_Article::query()->where('crawled','>', 0)->count();
        }
        
	public static function validate($factory)
	{
		$val = Validation::forge($factory);
		$val->add_field('blogid', 'Blogid', 'required|valid_string[numeric]');

		return $val;
	}
        
        public static function valid_link($link_url,$blog_url )
        {
            if(substr($link_url, 0,4) != 'http')
                if(substr($link_url, 0, 1) == '/')
                    return $link_url = $blog_url. substr($link_url, 1);
            return $link_url;
        }
        
        public static function article_by_url_exists($link_url)
        {
            $article = Model_Article::find('all', array('where' => array(
                    array('url', $link_url),
            )));

            return (sizeof($article));
        }
        
        public static function find_blog_articles($blog_id, $count = 5)
        {
            $articles = Model_Article::find('all', array('where' => array(
                    array('blogid', $blog_id),
                    array('crawled', null),
            ),
                'limit' => $count
                ));
            return $articles;
        }
        
        
        public static function article_by_blogid($blog_id)
        {
            $articles = Model_Article::find('all', array('where' => array(
                    array('blogid', $blog_id),
            ),
                'limit' => 100
                ));
            return $articles;
        }
        
        public static function article_count_by_blog()
        {
            $blogs = Model_Blog::find('all');
            $blog_article_count = [];
            foreach ($blogs as $blog):
                $count = sizeof(Model_Article::find('all', 
                        array('where' => array('blogid' => $blog->id))));
                $parsed = sizeof(Model_Article::find('all', 
                        array('where' => array(
                            array('blogid' => $blog->id),
                            array('crawled', '!=', null))
                            )));
                $blog_article_count[$blog->id] = array('total' => $count, 'parsed' => $parsed, 'to_parse' =>$count - $parsed);
            endforeach;
            
            return $blog_article_count;
        }
        
        
        public static function ready_for_parse($count)
        {
            $articles = Model_Article::find('all', array('where' => array(
                    array('crawled', null),
            ),
                'limit' => $count
                ));
            return $articles;
        }
}
