<?php
use Orm\Model;

class Model_Archive extends Model
{
        /*
         elementid:
         *  1 - type of archive
         *  2 - element as link
         *  3 - pagination link
         */
	protected static $_properties = array(
		'id',
		'blogid',
		'elementid', 
		'element',
	);
        
        public static function elementids()
        {
            return array(   1 => 'type of archive',
                            2 => 'element as link',
                            3 => 'pagination link',
                            4 => 'navigation type pagination element'
                );
        }

	public static function validate($factory)
	{
		$val = Validation::forge($factory);
		$val->add_field('blogid', 'Blogid', 'required|valid_string[numeric]');
		$val->add_field('elementid', 'Elementid', 'required|valid_string[numeric]');
		$val->add_field('element', 'Element', 'required|max_length[100]');

		return $val;
	}
        
        public static function check_if_page_exists($url)
        {
                if (!$data = @file_get_contents($url)) {
                    return false;
                }
                else
                    return true;
        }
        
        public static function find_by_blog_id($blog_id)
        {
            $archive = Model_Archive::find('all', array('where' => array(
                    array('blogid', $blog_id),
            )));
            return $archive;
        }
        
        public static function archive_type($blog_id)
        {
            $archive = Model_Archive::find('all', array('where' => array(
                    array('blogid', $blog_id),
                    array('elementid', 1)
            )));
            return $archive;
        }
        
        public static function entry_exists($blog_id, $element_id)
        {
            $result = DB::select('*')->from('archives')
                    ->where('blogid', $blog_id)
                    ->and_where('elementid', $element_id)
                    ->execute();
            return (count($result)==0)? 0:1;
        }
        
        public static function detect($blog)
        {
            Package::load('simplehtml');
            $simple_html = new Simplehtml;
            
            $response_text = '';
            //Step 1 detect archive page
            $year = date('Y');
            $year--;
            $blog_url = $blog->url;
            if(mb_substr($blog_url, -1) != '/')
                $blog_url .= '/';
            $blog_url_previous = $blog_url.$year.'/';
            if(Model_Archive::check_if_page_exists($blog_url_previous))
            {
                if(Model_Archive::entry_exists($blog->id, 1))
                    $response_text .= '<p>Archive page already in DB</p>';
                else
                {
                    $archive = Model_Archive::forge(array(
                        'blogid' => $blog->id,
                        'elementid' => 1, //type of archive 
                        'element' => 'year'));
                    if ($archive and $archive->save())
                    {
                        $response_text .= '<p>Added archive type for blog</p>';
                        Session::set_flash('success', 'Added yearly archive type for blog '.$blog->url.'.');
                    }
                    else
                    {
                        Session::set_flash('error', 'Could not save archive type.');
                        Response::redirect('Archive');
                    }
                }
                $blog_url = $blog_url_previous; 
            }
            else 
            {	
                if(Model_Archive::entry_exists($blog->id, 1))
                    $response_text .= '<p>Archive page already in DB</p>';
                else
                {
                    //TODO implement form for this
                    if(!Model_Archive::check_if_page_exists($blog_url.date('Y').'/'))
                    {
                        Session::set_flash('error', 'Automatic archive page detection failed, please submit archive url. Tried '.$blog_url.date('Y').'/');
                        Response::redirect('Archive');
                    }
                    else
                    {
                            $response_text .= '<p>Archive page <b>detected</b>. Using yearly archives. '.$blog_url.'</p>';
                            $blog_url .= date('Y').'/';
                            $archive = Model_Archive::forge(array(
                                'blogid' => $blog->id,
                                'elementid' => 1, //type of archive 
                                'element' => 'year'));
                            if ($archive and $archive->save())
                            {
                                $response_text .= '<p>Added archive type for blog</p>';
                                Session::set_flash('success', 'Added archive type for blog '.$blog->url.'.');
                            }
                            else
                            {
                                Session::set_flash('error', 'Could not save archive type.');
                                Response::redirect('Archive');
                            }
                    }
                }
            }
            
            
            $html = $simple_html->file_get_html($blog_url);
            
            //Step 2 detect link to articles element
            if(Model_Archive::entry_exists($blog->id, 2))
                $response_text .= '<p>Link element already in DB</p>';
            else
            {
                //find element that contains links to articles from article archive list page
                $h1_titles = $html->find('h1');
                $h2_titles = $html->find('h2'); 
                $h3_titles = $html->find('h3');
                $h4_titles = $html->find('h4');
                $h5_titles = $html->find('h5');

                $title_count = array();
                $title_count['h1'] =  sizeof($h1_titles);
                $title_count['h2'] =  sizeof($h2_titles);
                $title_count['h3'] =  sizeof($h3_titles);
                $title_count['h4'] =  sizeof($h4_titles);
                $title_count['h5'] =  sizeof($h5_titles);

                $titles_with_links = array();
                $title_url = array();
                //titles array is not empty and has a link
                if (sizeof($h1_titles) and 
                        $h1_titles[0]->children and 
                        $h1_titles[0]->children[0]->getAttribute('href'))
                {
                        array_push($titles_with_links,'h1');
                        array_push($title_url, $h1_titles[0]->children[0]->getAttribute('href'));

                }

                if (sizeof($h2_titles) and 
                        $h2_titles[0]->children and 
                        $h2_titles[0]->children[0]->getAttribute('href'))
                {
                        array_push($titles_with_links,'h2');
                        array_push($title_url, $h2_titles[0]->children[0]->getAttribute('href'));
                }

                if (sizeof($h3_titles) and 
                        $h3_titles[0]->children and 
                        $h3_titles[0]->children[0]->getAttribute('href'))
                {
                        array_push($titles_with_links,'h3');
                        array_push($title_url, $h3_titles[0]->children[0]->getAttribute('href'));
                }

                if (sizeof($h4_titles) and 
                        $h4_titles[0]->children and 
                        $h4_titles[0]->children[0]->getAttribute('href'))
                {
                        array_push($titles_with_links,'h4');
                        array_push($title_url, $h4_titles[0]->children[0]->getAttribute('href'));
                }

                if (sizeof($h5_titles) and 
                        $h5_titles[0]->children and 
                        $h5_titles[0]->children[0]->getAttribute('href'))
                {
                        array_push($titles_with_links,'h5');
                        array_push($title_url, $h5_titles[0]->children[0]->getAttribute('href'));
                }	

                $article_url = '';
                if(sizeof($titles_with_links) == 1)
                {
                    $response_text .= '<p>Article link element <b>detected</b>. It is '.$titles_with_links[0]. ' first link url is: '.$title_url[0].'</p>';
                    $article_url = $title_url[0];
                    if(Model_Archive::entry_exists($blog->id, 2))
                        $response_text .= '<p>Link to article already in DB</p>';
                    else
                    {
                        $archive = Model_Archive::forge(array(
                            'blogid' => $blog->id,
                            'elementid' => 2, //link to article
                            'element' => $titles_with_links[0]));
                        if ($archive and $archive->save())
                        {
                            Session::set_flash('success', 'Added archive link to article for blog '.$blog->url.'.');
                            $response_text .= '<p>Added archive link to article for blog</p>';
                        }
                        else
                        {
                            Session::set_flash('error', 'Could not save archive link to article.');
                            Redirect('Archive');
                        }
                    }
                }
                else
                {
                    if(sizeof($titles_with_links > 1))
                    {
                        //TODO implement form for this
//                        echo 'Choose from found elements which one is correct <br>';
                        $response = array();
                        for ($i = 0; $i<sizeof($title_url); $i++)
                        {
//                                echo $titles_with_links[$i]. ' '.$title_url[$i].' '. $titles_with_links[$i] .'<br>';
                                $response[$titles_with_links[$i]] = $title_url[$i];
                        }
                        return array('choose' => $response, 'title' => 'Choose link to articles element', 'elementid' => 2, 'blogid' => $blog->id);
                    }
                    else
                        //TODO implement form for this
                        echo 'You have to manually find what element contains links';
                }
            }
            //Step 3  pagination detection
            if (!$data = @file_get_contents($blog_url.'page/2')) {
                //TODO implement form for this
                $blog_archive = Model_Archive::archive_type($blog->id);
                if($blog_archive)
                {
                    $blog_archive = reset($blog_archive);
                    $test_url = $blog->url.$blog_archive->element.'/2';
                    if(@file_get_contents($test_url))
                    {
                        $archive = Model_Archive::forge(array(
                            'blogid' => $blog->id,
                            'elementid' => 3, //link to article
                            'element' => $blog_archive->element));
                        $archive->save();
                        $response_text .= '<p>Pagination <b>detected</b>. Using '.$test_url.'</p>';
                        Session::set_flash('success', 'Added archive pagination'.$blog->url.'.');
                        $response_text .= '<p>Added archive pagination</p>';
                    }
                    else
                        $response_text .= '<p>Automatic pagination detection failed, please submit pagination part of url. '.$blog_url.'page/2' .'</p>';
                }
                else
                    $response_text .= '<p>Automatic pagination detection failed, please submit pagination part of url. '.$blog_url.'page/2' .'</p>';
            } 
            else 
            {
                $response_text .= '<p>Pagination <b>detected</b>. Using '.$blog_url.'page/2</p>';
                
                if(Model_Archive::entry_exists($blog->id, 3))
                    $response_text .= '<p>Pagination already in DB</p>';
                else
                {
                    $archive = Model_Archive::forge(array(
                        'blogid' => $blog->id,
                        'elementid' => 3, //link to article
                        'element' => 'page'));
                    if ($archive and $archive->save())
                    {
                        Session::set_flash('success', 'Added archive pagination'.$blog->url.'.');
                        $response_text .= '<p>Added archive pagination</p>';
                    }
                    else
                    {
                        Session::set_flash('error', 'Could not save archive pagination.');
                        Redirect('Archive');
                    }
                }
            }
            
            return $response_text;
        }
}