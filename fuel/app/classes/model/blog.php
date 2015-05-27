<?php
use Orm\Model;

class Model_Blog extends Model
{
	protected static $_properties = array(
		'id',
		'url',
		'status',
		'crawldate',
		'created_at',
		'updated_at',
                'author',
                'author_gender',
                'title_in_body_text'
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => false,
		),
	);

	public static function validate($factory)
	{
		$val = Validation::forge($factory);
		$val->add_field('url', 'Url', 'required|max_length[255]');
		return $val;
	}
        
        public static function get_count()
        {
            return Model_Blog::query()->count();
        }
        
        
        public static function get_article_url($blog_id)
        {
            $blog = Model_Blog::find($blog_id);
            
            if(is_null($blog))
                return null;
            $base_url = $blog->url;
            
            $archives = Model_Archive::find('all', array('where' => array(
                    array('blogid', $blog_id),
                    )));
            $archive_data = [];
            
            foreach ($archives as $archive_entry):
                $archive_data[$archive_entry->elementid] = $archive_entry->element;
            endforeach;
            
            $article_url = '';
            if(key_exists(1, $archive_data))
                if(key_exists(2, $archive_data))
                {
                    if($archive_data[1] == 'year')
                    {
                        $year = date('Y');
                        $year--;
                        $archive_url = $base_url.$year.'/';
                        $counter = 10;
                        echo $archive_url;
                        while(!Model_Archive::check_if_page_exists($archive_url)):
                            $year--;
                            $archive_url = $base_url.$year.'/';
                            $counter--;
                            if($counter == 0)
                                break;
                        endwhile;
                        if($counter == 0)
                            return null;
                    }
                    else {
                        if($archive_data[1] == 'empty')
                        {
                            $archive_url = $base_url;
                        }
                        elseif($archive_data[1] == 'navigation_link')
                        {
                            $link_element = $archive_data[3];
                            $archive_url = $base_url;
//                            exit();
                        }
                        else
                        {
                            $archive_url = $base_url.$archive_data[1].'/';
                        }
                    }
                    //TODO other archive types
                    
                    Package::load('simplehtml');
                    $simple_html = new Simplehtml;
                    $html = $simple_html->file_get_html($archive_url);
                    $article_urls = $html->find($archive_data[2]);
                    
                    if(sizeof($article_urls) == 0)
                    {
                        echo "no articles found in ". $archive_url. ' !';
                        exit();
                    }
                    
                    $response = $article_urls[0]->find('a'); //changed from 0 because 0 index could be logo

                    if(!key_exists(0, $response))
                        $response = $article_urls[1]->find('a');
                    if(!key_exists(0, $response))
                        $response = $article_urls[3]->find('a');    
                    
                    $response_one = $response[0]->getAttribute ('href');
                    
                    if($response_one == '#')
                    {
                        //case when heading has # as first href link, and only 2nd is actual link to article
                        $response_one = $response[1]->getAttribute ('href');
                    }
                    $response = $response_one;
                    
                    //is relative link
                    if(substr($response, 0,4) != 'http')
                    {
                        if (substr($response, 0,1 != '/'))
                                $response = $base_url.$response;
                        else
                            // don't  have two slashes
                            $response = substr($base_url, 0,-1).$response;
                    }
                            
                    return $response; 
                }
            return null;    
        }
        
        public static function urls()
        {
            $blog = Model_Blog::find('all');
            $arr = [];
            foreach ($blog as $one):
                if(substr($one['url'], 0,5) == 'https')
                    $url = str_replace(array('https://', '/'), '', $one['url']);
                else
                    $url = str_replace(array('http://', '/'), '', $one['url']);
                $arr[$one['id']] = $url;
            endforeach;
                
            return $arr;
        }
}
