<?php
class Controller_Articleexclude extends Controller_Template 
{
        public function before() {
            parent::before();    
            //send page name to navigation to mark active page button
            $this->template->page = $data['page'] = 'settings';
            $this->template->navigation = View::forge('navigation', $data); 
        }
        
	public function action_index($blog_id = null, $article_id = null)
	{
                if($blog_id == null)
                {
                    Session::set_flash('error', 'Missing blog id!');
                    Response::redirect('blog');
                }
                if($article_id == null)
                {
                    $article_url = Model_Blog::get_article_url($blog_id); 
//                    echo $article_url;
//                    exit();
                }
                else
                {
                    $article = Model_Article::find($article_id);
                    if(!$article)
                    {
                        Session::set_flash('error', 'Failed search of article');
                        Response::redirect('blog');
                    }
                    $article_url = $article->url;
                }
                
                
                $data['articleexcludes'] = Model_Articleexclude::find('all', array(
                        'where' => array(
                            array('blogid', $blog_id),
                        ),
                        'order_by' => array(
                            'blogid' => 'asc',
                            'ruleid' => 'asc',
                        ),
                    ));
                
//                echo $article_url;
//                exit();
                $data['blog_urls'] = Model_Blog::urls();
                $data['article_url']= $article_url;
                $data['post'] = Model_Articleexclude::get_article_wrapper($article_url,$blog_id);
                $data['comment'] = Model_Articleexclude::get_article_comments($article_url,$blog_id); 
                $data['blog_id'] = $blog_id;
                $data['filtered_ids'] = Model_Articleexclude::filtered_ids($blog_id);
                $data['filtered_classes'] = Model_Articleexclude::filtered_classes($blog_id);
                $data['rules'] = Model_Articleexclude::rules();

		$this->template->title = "Article parse rules";
		$this->template->content = View::forge('articleexclude/index', $data)->auto_filter(false);

	}
        
        public function action_preview($blog_id = null)
        {
            if($blog_id == null)
            {
                Session::set_flash('error', 'Missing blog id!');
                Response::redirect('blog');
            }
            $data['articleexcludes'] = Model_Articleexclude::find('all', array(
                'where' => array(
                    array('blogid', $blog_id),
                ),
                'order_by' => array(
                    'blogid' => 'asc',
                    'ruleid' => 'asc',
                ),
                ));

            $data['blog_urls'] = Model_Blog::urls();
            $article_url = Model_Blog::get_article_url($blog_id);

            $data['article_url']= $article_url;
            $data['content_element'] = Model_Articleexclude::get_article_content_element($article_url,$blog_id);
            $data['blog_id'] = $blog_id;
            $data['filtered_ids'] = Model_Articleexclude::filtered_ids($blog_id);
            $data['filtered_classes'] = Model_Articleexclude::filtered_classes($blog_id);

            $this->template->title = "Article parse rules";
            $this->template->content = View::forge('articleexclude/preview', $data)->auto_filter(false);
        }
        
        
	public function action_view($blog_id = null)
	{
		$data['articleexclude'] = Model_Articleexclude::find($id);

		is_null($id) and Response::redirect('Articleexclude');

		$this->template->title = "Articleexclude";
		$this->template->content = View::forge('articleexclude/view', $data);

	}

	public function action_create()
	{
                if(input::is_ajax())
                {
                    $input = Input::all(); 
                    $element = $input['element'];
                    $action = $input['action'];
                    $type = $input['type'];
                    $blog_id = $input['blog_id'];
                    $offset = $input['offset'];
                    $length = $input['length'];
                    
                    $rule_id = 0;
                    switch($action):
                        case 'Remove':
                            if($type == 'id') 
                                $rule_id = 1;
                            else
                                $rule_id = 2;
                            break;
                            
                        case 'Add':
                            //TODO remove this exclude rule
                            return json_encode(array('status' => 'nok'));
                            break;
                        
                        case 'Author':
                            $rule_id = 3;
                            if($type == 'class')
                                $element = '.'.$element;
                            else
                                $element = '#'.$element;
                            break;
                        
                        case 'Published':
                            $rule_id = 4;
                            if($type == 'class')
                                $element = '.'.$element;
                            else
                                $element = '#'.$element;
                            break;
                            
                        case 'Content':
                            if($type == 'id') 
                                $rule_id = 5;
                            else
                                $rule_id = 6;
                            break;
                            
                        case 'Comment_author':
                            if($type == 'class')
                                $element = '.'.$element;
                            else
                                $element = '#'.$element;
                            $rule_id = 7;
                            break;
                            
                        case 'Comment_content':
                            if($type == 'class')
                                $element = '.'.$element;
                            else
                                $element = '#'.$element;
                            $rule_id = 8;
                            break;
                    endswitch;
                    
                    if($rule_id == 0)
                        return json_encode(array('status' => 'nok '.$action));
                    
                    $articleexclude = Model_Articleexclude::forge(array(
                            'blogid' => $blog_id,
                            'ruleid' => $rule_id,
                            'element' => $element,
                            'offset' => $offset,
                            'length' => $length
                    ));
                    if ($articleexclude and $articleexclude->save())
                        return json_encode(array('status' => 'ok'));
                    else
                        return json_encode(array('status' => 'nok'));
                }

		$this->template->title = "Articleexcludes";
		$this->template->content = View::forge('articleexclude/create');

	}

	public function action_edit($id = null)
	{
		is_null($id) and Response::redirect('Articleexclude');

		$articleexclude = Model_Articleexclude::find($id);

		$val = Model_Articleexclude::validate('edit');

		if ($val->run())
		{
//			$articleexclude->blogid = Input::post('blogid');
			$articleexclude->ruleid = Input::post('ruleid');
			$articleexclude->element = Input::post('element');

			if ($articleexclude->save())
			{
				Session::set_flash('success', 'Updated articleexclude #' . $id);

				Response::redirect('articleexclude');
			}

			else
			{
				Session::set_flash('error', 'Could not update articleexclude #' . $id);
			}
		}

		else
		{
			if (Input::method() == 'POST')
			{
//				$articleexclude->blogid = $val->validated('blogid');
				$articleexclude->ruleid = $val->validated('ruleid');
				$articleexclude->element = $val->validated('element');

				Session::set_flash('error', $val->error());
			}

			$this->template->set_global('articleexclude', $articleexclude, false);
		}

		$this->template->title = "Articleexcludes";
		$this->template->content = View::forge('articleexclude/edit');

	}

	public function action_delete($id = null)
	{
		if ($articleexclude = Model_Articleexclude::find($id))
		{
			$articleexclude->delete();

			Session::set_flash('success', 'Deleted articleexclude #'.$id);
		}

		else
		{
			Session::set_flash('error', 'Could not delete articleexclude #'.$id);
		}

		Response::redirect('articleexclude');

	}


}