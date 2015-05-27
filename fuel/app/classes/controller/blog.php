<?php
class Controller_Blog extends Controller_Template 
{
        public function before() {
            parent::before();    
            //send page name to navigation to mark active page button
            $this->template->page = $data['page'] = 'settings';
            $this->template->navigation = View::forge('navigation', $data); 
        }
        
	public function action_index()
	{
		$data['blogs'] = Model_Blog::find('all', array('order_by' => array('status'=> 'asc' , 'id' => 'desc' ))); 
                
		$this->template->title = "Blogs";
		$this->template->content = View::forge('blog/index', $data);

	}

	public function action_view($id = null)
	{
		$data['blog'] = Model_Blog::find($id);

		is_null($id) and Response::redirect('Blog');

		$this->template->title = "Blog";
		$this->template->content = View::forge('blog/view', $data);

	}

	public function action_create()
	{
		if (Input::method() == 'POST')
		{
			$val = Model_Blog::validate('create');
			
			if ($val->run())
			{
				$blog = Model_Blog::forge(array(
					'url' => Input::post('url'),
					'status' => 0,
					'crawldate' => 0,
				));

				if ($blog and $blog->save())
				{
					Session::set_flash('success', 'Added blog #'.$blog->id.'.');

					Response::redirect('blog');
				}

				else
				{
					Session::set_flash('error', 'Could not save blog.');
				}
			}
			else
			{
				Session::set_flash('error', $val->error());
			}
		}

		$this->template->title = "Blogs";
		$this->template->content = View::forge('blog/create');

	}

	public function action_edit($id = null)
	{
		is_null($id) and Response::redirect('Blog');

		$blog = Model_Blog::find($id);

		$val = Model_Blog::validate('edit');

		if ($val->run())
		{
			$blog->url = Input::post('url');

			if ($blog->save())
			{
				Session::set_flash('success', 'Updated blog #' . $id);

				Response::redirect('blog');
			}

			else
			{
				Session::set_flash('error', 'Could not update blog #' . $id);
			}
		}

		else
		{
			if (Input::method() == 'POST')
			{
				$blog->url = $val->validated('url');
				Session::set_flash('error', $val->error());
			}

			$this->template->set_global('blog', $blog, false);
		}

		$this->template->title = "Blogs";
		$this->template->content = View::forge('blog/edit');

	}

	public function action_delete($id = null)
	{
		if ($blog = Model_Blog::find($id))
		{
			$blog->delete();

			Session::set_flash('success', 'Deleted blog #'.$id);
		}

		else
		{
			Session::set_flash('error', 'Could not delete blog #'.$id);
		}

		Response::redirect('blog');

	}


}