<?php
class Controller_Article extends Controller_Template 
{
        
         public function before() {
            parent::before();    
            //send page name to navigation to mark active page button
            $this->template->page = $data['page'] = 'articles';
            $this->template->navigation = View::forge('navigation', $data); 
        }
        
        public function action_index()
	{
                if (Input::method() == 'POST')
                {
                    
                    $count = Input::post('parse_count');
                    Response::redirect('article/parse/'.$count);
                }
                $data['blog_urls'] = Model_Blog::urls();
                $data['article_count'] = Model_Article::article_count_by_blog();
		$this->template->title = "Articles";
		$this->template->content = View::forge('article/index', $data);

	}
        
	public function action_blogarticles($blog_id = null)
	{
                if(!$blog_id)
                    Session::set_flash('error', 'Missing blog id, empty results due to that.');
		$data['articles'] = Model_Article::article_by_blogid($blog_id);
                $blog = Model_Blog::find($blog_id);
                $data['blog_url'] = $blog->url;
                $data['blog_id'] = $blog_id;
		$this->template->title = "Articles";
		$this->template->content = View::forge('article/blogarticles', $data);

	}
        
	//remove dublicate paragraphs
	public function action_cleanup()
	{
				//iterate over some range
                for($i=50000; $i<68811; $i++) 
                {
                    $paragraph = Model_Paragraph::find($i);
                    if($paragraph)
                        Model_Paragraph::remove_dublicate($paragraph);
                }
                echo 'done';
                exit();  
	}
    
	//remove duplicate comments
	public function action_cleanup2()
	{
				//iterate over some range
                for($i=11000; $i<21000; $i++)
                {
                    $comment = Model_Comment::find($i);
                    if($comment)
                        Model_Comment::remove_dublicate($comment);
                }
                echo 'done';
                exit();  
	}
        
	public function action_view($id = null)
	{
		$data['article'] = Model_Article::find($id);

		is_null($id) and Response::redirect('Article');

		$this->template->title = "Article";
		$this->template->content = View::forge('article/view', $data);

	}
        
        //search for articles in blog arhive pages
	public function action_create($blog_id = null, $start_from_year = null, $debug = null)
	{
            //$debug= true;
                
                $start = time();
                if(!$blog_id)
                {
                    Session::set_flash('error', 'Missing blogId');
                    Response::redirect('Article');
                }
                    
                $blog = Model_Blog::find($blog_id);
                $blog_archive = Model_Archive::find_by_blog_id($blog_id);
                $archive_arr = [];
                foreach ($blog_archive as $archive_item)
                    $archive_arr[$archive_item->elementid] = $archive_item->element;

                $archive_type = $archive_arr[1];
                $link_element = $archive_arr[2];
                $pagination_type = $archive_arr[3];

                if($archive_type)
                {
                    $article_count = 0;
                    Package::load('simplehtml');
                    $year = date('Y');
                    
                    if($start_from_year)
                        $year = $start_from_year;
                    $archive_url = $blog->url.$year;
                    if($archive_type == 'empty')
                        $archive_url = $blog->url;
                    elseif($archive_type != 'year')
                        $archive_url = $blog->url.$archive_type.'/1';
                    $page_nr = 1;
                    
                    if(($archive_type == 'empty' or $archive_type != 'year' ) and $start_from_year)
                        $page_nr = $start_from_year;
                    
                    $failed_nr = 0;
                    
                    if($archive_type == 'navigation_link')
                        $archive_url = $blog->url;
//                    var_dump($archive_url);
//                        exit();
                    
                    if (!Model_Archive::check_if_page_exists($archive_url))
                    {
                        $year--;
                        $archive_url = $blog->url.$year; 
                    }
                        
                    //iterate years
                    while(Model_Archive::check_if_page_exists($archive_url)):
                        $archive_url_paged = $archive_url.'/'.$pagination_type.'/'.$page_nr;
                    
                        if($page_nr == 1)
                            $archive_url_paged = $archive_url; //.'/'.$pagination_type.'/'.$page_nr;
                        if($debug)
                        {
                            echo $pagination_type.' '.$link_element .'<br>';
                            echo $archive_url_paged;
                        }
                        if($archive_type == 'empty')
                            $archive_url_paged = $archive_url.'page/'.$page_nr;
                        elseif($archive_type != 'year' and $archive_type != 'navigation_link')
                            $archive_url_paged = $blog->url.$archive_type.'/'.$page_nr;
                        
                        //iterate pages
                        while(Model_Archive::check_if_page_exists($archive_url_paged)):
                            $simple_html = new Simplehtml;
                            try{
                                $html = $simple_html->file_get_html($archive_url_paged);
                            } catch (Exception $ex) {
                                $html = $simple_html->file_get_html($archive_url_paged);
                            }
                            
                            $link_list = $html->find($link_element);
                            $next_page_link = '';
                            if($archive_type == 'navigation_link')
                            {
                                $next_page_link = $html->find($pagination_type);
                                $next_page_link = reset($next_page_link);
                                if(method_exists($next_page_link,'getAttribute'))
                                {
                                    $next_page_link = $next_page_link->getAttribute('href');
                                    $next_page_link = Model_Article::valid_link($next_page_link,$blog->url );
                                }
                                else
                                {
                                    if(method_exists ( $next_page_link , 'first_child' ))
                                    {
                                        $next_page_link = $next_page_link->first_child()->getAttribute('href');
                                        $next_page_link = Model_Article::valid_link($next_page_link,$blog->url );
                                    }
                                    else {
                                        $dump = var_export($next_page_link, true);
                                        Session::set_flash('error', 'Added total number of articles:'.$article_count.' last page parsed = '.$archive_url_paged. ' Exit with next_page_link = '.$dump);
                                        Response::redirect('article');
                                    }
                                }
                            }
                                
                            
                            if($debug)
                            {
                                foreach($link_list as $link):
                                    echo $link->plaintext;
                                    if($link->first_child())
                                    {
                                        var_dump($link->first_child()->getAttribute('href'));
                                        var_dump($link->getAttribute('href'));
                                    }
                                endforeach;
                                
                                exit();
                            }
							
                            //iterate links in page
                            foreach($link_list as $link)
                            {
                                
                                $title = html_entity_decode(trim($link->plaintext),ENT_QUOTES);
                                
                                if($link->first_child())
                                {
                                    $link_url = $link->first_child()->getAttribute('href');
                                    
                                    if($link_url == '#')
                                    {
                                        //case when heading has # as first href link, and only 2nd is actual link to article
                                        $link_url = $link->first_child()->next_sibling()->getAttribute('href');
                                    }
                                }
                                else
                                {
                                    continue;
                                    echo 'skipping link'. $title;
                                }
                                
                                if($link_url == $blog->url )
                                    continue;
                                
                                $link_url = Model_Article::valid_link($link_url,$blog->url );
                                $url1 = parse_url($link_url);
                                $url2 = parse_url($blog->url);
                                
                                if(key_exists('host', $url1) and key_exists('host', $url2))
                                {
                                    if($url1['host'] !== $url2['host']){
                                        echo $link_url.' outside site domain!!!!'. $blog->url .' ---- hosts:'.$url1['host'] .' vs '. $url2['host']. '<br>';
                                        continue;
                                    }
                                }
                                else
                                {
                                    echo 'no hosts for '.$link_url . ' '.$blog->url.'<br>';
                                    continue;
                                }
                                try{
                                    //don't add duplicates
                                    if(!Model_Article::article_by_url_exists($link_url))
                                    {
                                        if($debug)
                                        {
                                            echo $link_url;
                                            exit();
                                        }
                                        
                                        $article = Model_Article::forge(array(
                                            'blogid' => $blog_id,
                                            'author' => NULL,
                                            'url' => $link_url,
                                            'title' => $title,
                                            'published' => NULL,
                                            'crawled' => NULL,
                                        ));
                                        if (!$article->save())
                                            $failed_nr++;
                                        $article_count++;
                                    }
                                    else
                                    {
                                        echo 'duplicate link: '.$link_url.'<br>';
                                    }
                                } catch (Exception $ex) {
                                    //duplicate or some other error
                                    echo 'there was an error saving '.$link_url.'<br>';
                                    var_dump($ex);
//                                    exit();
                                }
                            }
                            $page_nr++;
                            $archive_url_paged = $archive_url.'/'.$pagination_type.'/'.$page_nr;
                            if($archive_type != 'year' and $archive_type != 'navigation_link')
                                $archive_url_paged = $blog->url.$archive_type.'/'.$page_nr;
                            if($archive_type == 'empty')
                                $archive_url_paged = $archive_url.'page/'.$page_nr;
                            
                            if($archive_type == 'navigation_link')
                            {
                                $archive_url_paged = $next_page_link;
                            }
                        endwhile;

                        $year--;
                        $page_nr = 1;
                        $archive_url = $blog->url.$year;
                        
                        if($archive_type == 'empty' or $archive_type == 'navigation_link')
                            break;
                        
                        //in case of gap years try skipping up to 5 years
                        if(!Model_Archive::check_if_page_exists($archive_url))
                            for($i = 0; $i< 3; $i++)
                                if(!Model_Archive::check_if_page_exists($archive_url))
                                {
                                    $year--;
                                    $archive_url = $blog->url.$year;
                                }
                                else
                                    break;
                        
                    endwhile;  
                    
                    if($failed_nr > 0)
                        Session::set_flash('error', 'Failed saving '.$failed_nr.' article links');
                    else
                    {
                        $time_total = Date::time_ago($start);
                        Session::set_flash('success', 'Added total number of articles:'.$article_count.' last page parsed = '.$archive_url_paged. ' operation started '.$time_total);
                    }
                }
                Response::redirect('article');

		$this->template->title = "Articles";
		$this->template->content = View::forge('article/create');

	}

	public function action_edit($id = null)
	{
		is_null($id) and Response::redirect('Article');

		$article = Model_Article::find($id);

		$val = Model_Article::validate('edit');

		if ($val->run())
		{
			$article->blogid = Input::post('blogid');
			$article->author = Input::post('author');
			$article->url = Input::post('url');
			$article->published = Input::post('published');
			$article->crawled = Input::post('crawled');

			if ($article->save())
			{
				Session::set_flash('success', 'Updated article #' . $id);

				Response::redirect('article');
			}

			else
			{
				Session::set_flash('error', 'Could not update article #' . $id);
			}
		}

		else
		{
			if (Input::method() == 'POST')
			{
				$article->blogid = $val->validated('blogid');
				$article->author = $val->validated('author');
				$article->url = $val->validated('url');
				$article->published = $val->validated('published');
				$article->crawled = $val->validated('crawled');

				Session::set_flash('error', $val->error());
			}

			$this->template->set_global('article', $article, false);
		}

		$this->template->title = "Articles";
		$this->template->content = View::forge('article/edit');

	}

	public function action_delete($id = null)
	{
		if ($article = Model_Article::find($id))
		{
			$article->delete();

			Session::set_flash('success', 'Deleted article #'.$id);
		}

		else
		{
			Session::set_flash('error', 'Could not delete article #'.$id);
		}

		Response::redirect('article');

	}
        
        public function action_parse($count = 1, $blog_id = null, $article_id = null)
	{
            $start = time();
            Package::load('simplehtml');
            $lib_path = APPPATH.'language_detect\lib\TextLanguageDetect';
            Fuel::load($lib_path.'\TextLanguageDetect.php');
            Fuel::load($lib_path.'\LanguageDetect\TextLanguageDetectParser.php');
            Fuel::load($lib_path.'\LanguageDetect\TextLanguageDetectISO639.php');
            $l = new TextLanguageDetect\TextLanguageDetect;
            $l->setNameMode(2);
            
            $to_parse = '';
            if($blog_id != null)
            {
                if($article_id != null)
                    $to_parse = array( 0=> Model_Article::find($article_id));
                else
                    $to_parse = Model_Article::find_blog_articles($blog_id,$count);
            }
            else
                $to_parse = Model_Article::ready_for_parse($count);
            
            $blog_urls = Model_Blog::urls();
            
            function output($child,$file_url,$article_id,$paragraph_nr, $l)
            {
                $child_text = html_entity_decode(trim($child->plaintext),ENT_QUOTES);
                if($child_text == '')
                    return;
//                    echo $child;

                $text = trim(html_entity_decode($child->plaintext, ENT_NOQUOTES , 'UTF-8'));
                $spaces = substr_count($text, ' ');
                $number_of_words = $spaces+1; //10;
                $number_of_symbols_in_paragraph = mb_strlen($text);
                
                $paragraph = Model_Paragraph::forge(array(
                    'text' => $text,
                    'articleid' => $article_id,
                    'symbols' => $number_of_symbols_in_paragraph,
                    'spaces' => $spaces
                ));
                $paragraph->save();
                
                
                $result = $l->detect($text, 4);

                $languages = array();
                $counter = 1;
                foreach($result as $lang => $probability):
                    $languages['language_'.$counter] = array('language'=>$lang, 'possibility'=>$probability);
                    $counter ++;
                    $paragraph_lang = Model_Paragraphlang::forge(array(
                        'paragraphid' => $paragraph->id,
                        'language' => $lang,
                        'probability' => $probability
                    ));
                    $paragraph_lang->save();
                endforeach;

                
                if($spaces > 0)
                    $avg_number_of_symbols_in_a_word = $number_of_symbols_in_paragraph/$spaces;
                else
                    $avg_number_of_symbols_in_a_word = $number_of_symbols_in_paragraph;

                File::append($file_url, $article_id.'.txt', '['.$paragraph_nr.']  '.$text."\r\n"."\r\n");
                return array('languages' => $languages , 
                        'number_of_symbols_in_paragraph' => $number_of_symbols_in_paragraph, 
                        'number_of_spaces' => $spaces,
                        'number_of_words' => $number_of_words,
                        'avg_number_of_symbols_in_a_word' => $avg_number_of_symbols_in_a_word
                    );
            }

            $parsed_count = 0;
//            var_dump($to_parse);
//            exit();
            foreach ($to_parse as $parse_article):
                echo 'Parsed '. $parse_article->id.' '.$parse_article->url.'<br>';
                $parse_article->blogid;
                $content_element = Model_Articleexclude::get_article_content_element($parse_article->url,$parse_article->blogid);
                $filtered_ids = Model_Articleexclude::filtered_ids($parse_article->blogid);
                $filtered_classes = Model_Articleexclude::filtered_classes($parse_article->blogid);
            
                $simple_html = new Simplehtml;
                $html = $simple_html->file_get_html($parse_article->url);
                if(!$html)
                    continue;
                $html_full = $html;
                $published = '';
                if($parse_article->published == null)
                {
                    $published_element_for_blog = Model_Articleexclude::get_published_element($parse_article->blogid);
                    if($published_element_for_blog)
                    {
                        $published_date = $html->find($published_element_for_blog->element);
                        $published_date = reset($published_date);
                        if($published_element_for_blog->offset)
                        {
                            $published_date = substr($published_date,$published_element_for_blog->offset);
                            echo $published_date;
                        }
//                        echo $published_date->plaintext;
                        if (date('Y-m-d', strtotime($published_date))== '1970-01-01')
                        {
                            // date is in latvian string format, try to get it by parts
                            preg_match_all('!\d+!', $published_date, $matches);
                            $matches = reset($matches);
                            if(key_exists(2, $matches))
                                $published = $matches[0].'-'.$matches[1].'-'.$matches[2];
                            else
                            {
                                if(key_exists(0, $matches))
                                    $published = $matches[0].'-01-01';
                                else
                                    $published = date('Y-m-d', strtotime($published_date));
                            }
                        }
                        else
                            $published  = date('Y-m-d', strtotime($published_date));
                        
                        $parse_article->published = $published;
                        $parse_article->save();
                    }
                }
                
                $author_element = Model_Articleexclude::get_article_author_element($parse_article->blogid);
                if($author_element)
                {
                    if($author_element->element)
                        $author = $html->find($author_element->element);
//                    var_dump($author);
                    $author = reset($author);
                    if($author)
                    {
                        $author = html_entity_decode(trim($author->plaintext,ENT_QUOTES));
                        if($author_element->offset)
                            $author = trim(substr($author,$author_element->offset));
                        $author = preg_replace('/(\s\s+|\t|\n)/', ' ', $author); //remove doulbe whitespaces, tabs/ newlines
                        
                    }
                        
                    $parse_article->author = $author;
                    $parse_article->save();
                }
                    
                        
                //need to parse this before meta data is taken out
                $comment_author_element = Model_Articleexclude::get_comments_author_element($parse_article->blogid);
                $comment_authors = $html_full->find($comment_author_element);
                $author_array = array();
                foreach($comment_authors as $comment_author):
                    $comment_auth = html_entity_decode(trim($comment_author->plaintext),ENT_QUOTES);
                    $comment_auth = preg_replace('/(\s\s+|\t|\n)/', ' ', $comment_auth);
                    array_push($author_array, $comment_auth);
                endforeach;
                
                
                foreach ($filtered_classes as $taken_out_class => $true)
                {
                        foreach ($html->find('.'.$taken_out_class) as $node)
                        {
                                $node->outertext = '';
                        }
                        $html->load($html->save());
                }

                foreach ($filtered_ids as $taken_out_id => $true)
                {
                        foreach ($html->find('#'.$taken_out_id) as $node)
                        {
                                $node->outertext = '';
                        }
                        $html->load($html->save());
                }

                foreach ($html->find('img') as $node)
                {
                        $node->outertext = '[bilde]';
                }
                $html->load($html->save());

                $article_id = $parse_article->id;
                
                
                if($content_element == 'article')
                    $html = $html->getElementByTagName($content_element);
                else
                {
                    $html = $html->find($content_element);
                    $html = reset($html);
                }
                
                
                $file_url = DOCROOT.'/'.$blog_urls[$parse_article->blogid].'/';
                try{
                    File::create_dir(DOCROOT.'/', $blog_urls[$parse_article->blogid], 7777);
                } catch (Exception $ex) {
                    //folder already exists
                }

                File::update($file_url, $article_id.'.txt', ''); //create file
                File::update($file_url, $article_id.'.xml', ''); //create file
                File::update($file_url, $article_id.'_comments.txt', ''); //create file
                
                $paragraph_nr = 1;

                $title = trim($parse_article->title);

                $paragraphs = array();
                
            if(is_object($html))
            {
                foreach($html->childNodes() as $child):
                    if(trim($child->plaintext) == '')
                        continue;
                    if($child->first_child())
                    {
                        $tag = $child->first_child()->tag;
                        //if children are p or div output them as separate paragraphs
                        if($tag == 'p' or $tag == 'div')
                        {
                            $children_of_child = $child->childNodes();
                            $all_same = true;
                            foreach ($children_of_child as $second_child)
                            {
                                $paragraphs['paragraph_'.$paragraph_nr] = output($second_child,$file_url,$article_id,$paragraph_nr,$l);  
                                $paragraph_nr++;
                            }
                        }
                        else   
                        {
                           $paragraphs['paragraph_'.$paragraph_nr] = output($child,$file_url,$article_id,$paragraph_nr,$l);
                        }
                    }
                    else
                    {
                        $paragraphs['paragraph_'.$paragraph_nr] = output($child,$file_url,$article_id,$paragraph_nr,$l);  
                    }

                    $paragraph_nr++;
                endforeach;
            }
            else
            {
                continue;
            }

                //language of all article
                $result = $l->detect($html->plaintext, 4);
                $languages = array();
                $counter = 1;
                foreach($result as $lang => $probability):
                    $languages['language_'.$counter] = array('language'=>$lang, 'possibility'=>$probability);
                    $counter ++;
                endforeach;

                $meta_array = array('article' => array('title'=>$title, 'published'=>$published, 'retrieved'=>date('Y-m-d'), 'article_link' => $parse_article->url ,'article_languages' => $languages, 'paragraphs' => $paragraphs));
                $xml = Format::forge($meta_array)->to_xml();
                //META
                File::append($file_url, $article_id.'.xml', $xml);
                
                $parse_article->crawled = date('Y-m-d');
                if (!$parse_article->save())
                {
                    Session::set_flash('error', 'Failed saving article parse date' . $parse_article->url);
                    Response::redirect('article');
                }   
                else
                    $parsed_count++;
                
                //parse comments
                $comment_element = Model_Articleexclude::get_comments_content_element($parse_article->blogid);
                $comments = $html_full->find($comment_element);
                $comment_array = array();

                foreach($comments as $comment):
                    $comment_text = html_entity_decode(trim($comment->plaintext), ENT_QUOTES);
                    $comment_text = preg_replace('/(\s\s+|\t|\n)/', ' ', $comment_text); //remove doulbe whitespaces, tabs/ newlines
                    array_push($comment_array, $comment_text);
                endforeach;
                
                for($i = 0; $i<sizeof($comment_array); $i++):
                    if(key_exists($i, $author_array))   
                        $comment_object = Model_Comment::forge(array(
                            'text' => $comment_array[$i],
                            'articleid' => $parse_article->id,
                            'author' => $author_array[$i]
                        ));
                    else
                        $comment_object = Model_Comment::forge(array(
                            'text' => $comment_array[$i],
                            'articleid' => $parse_article->id,
                            'author' => ''
                        ));
                    $comment_object->save();
                    File::append($file_url, $article_id.'_comments.txt', '['. (key_exists($i, $author_array)? $author_array[$i] : ' ').']  '.$comment_array[$i]."\r\n"."\r\n"); //create file
                endfor;
                
            endforeach;
            
            $time_total = Date::time_ago($start);
            Session::set_flash('success', 'Parsed '.$parsed_count.' articles in '. $time_total );
            if($blog_id)
                Response::redirect('article/blogarticles/'.$blog_id);
            else
                Response::redirect('article');

	}

}