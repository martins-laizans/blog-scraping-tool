<?php

class Controller_Archive extends Controller_Template 
{
        public function before() {
            parent::before();    
            //send page name to navigation to mark active page button
            $this->template->page = $data['page'] = 'settings';
            $this->template->navigation = View::forge('navigation', $data); 
        }
        
        public function action_auto($blog_id = null)
	{
                if (Input::method() == 'POST')
                {   
                    $elementid = Input::post('elementid');
                    $choice = Input::post('choice');
                    $blogid = Input::post('blogid'); 
                    
                    $archive = Model_Archive::forge(array(
                            'blogid' => $blogid,
                            'elementid' => $elementid,
                            'element' => $choice,
                    ));
                    $archive->save();
                }
                $blog = Model_Blog::find($blog_id);
		is_null($blog) and Response::redirect('Archive');
                
                //detect elements from blog URL
                $data['archive_response'] = Model_Archive::detect($blog);
                
                $data['blog_url'] = $blog->url;
                $data['blogid'] = $blog_id;
                if(is_array ($data['archive_response']) and key_exists('choose', $data['archive_response']))
                {
                    //show form to choose
                    $this->template->content = View::forge('archive/choose', $data);
                }
                else    
                    $this->template->content = View::forge('archive/auto', $data);
		
                
		$this->template->title = "Archive pages";
		
	}
        
	public function action_index()
	{       
		$data['blog_urls'] = Model_Blog::urls();
		$data['element_ids'] = Model_Archive::elementids();
		$data['archives'] = Model_Archive::find('all');
		$this->template->title = "Archive pages";
		$this->template->content = View::forge('archive/index', $data);
	}

	public function action_view($id = null)
	{
		$data['archive'] = Model_Archive::find($id);

		is_null($id) and Response::redirect('Archive');

		$this->template->title = "Archive";
		$this->template->content = View::forge('archive/view', $data);

	}

	public function action_create()
	{
		if (Input::method() == 'POST')
		{
			$val = Model_Archive::validate('create');
			
			if ($val->run())
			{
				$archive = Model_Archive::forge(array(
					'blogid' => Input::post('blogid'),
					'elementid' => Input::post('elementid'),
					'element' => Input::post('element'),
				));

				if ($archive and $archive->save())
				{
					Session::set_flash('success', 'Added archive #'.$archive->id.'.');

					Response::redirect('archive');
				}

				else
				{
					Session::set_flash('error', 'Could not save archive.');
				}
			}
			else
			{
				Session::set_flash('error', $val->error());
			}
		}

		$this->template->title = "Archives";
		$this->template->content = View::forge('archive/create');

	}

	public function action_edit($id = null)
	{
		is_null($id) and Response::redirect('Archive');

		$archive = Model_Archive::find($id);

		$val = Model_Archive::validate('edit');

		if ($val->run())
		{
			$archive->blogid = Input::post('blogid');
			$archive->elementid = Input::post('elementid');
			$archive->element = Input::post('element');

			if ($archive->save())
			{
				Session::set_flash('success', 'Updated archive #' . $id);

				Response::redirect('archive');
			}

			else
			{
				Session::set_flash('error', 'Could not update archive #' . $id);
			}
		}

		else
		{
			if (Input::method() == 'POST')
			{
				$archive->blogid = $val->validated('blogid');
				$archive->elementid = $val->validated('elementid');
				$archive->element = $val->validated('element');

				Session::set_flash('error', $val->error());
			}

			$this->template->set_global('archive', $archive, false);
		}

		$this->template->title = "Archives";
		$this->template->content = View::forge('archive/edit');

	}

	public function action_delete($id = null)
	{
		if ($archive = Model_Archive::find($id))
		{
			$archive->delete();

			Session::set_flash('success', 'Deleted archive #'.$id);
		}

		else
		{
			Session::set_flash('error', 'Could not delete archive #'.$id);
		}

		Response::redirect('archive');

	}


}