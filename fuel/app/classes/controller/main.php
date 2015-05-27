<?php

class Controller_Main extends Controller_Template
{
     public function before() {
        parent::before();    
        //send page name to navigation to mark active page button
        $this->template->page = $data['page'] = 'main';
        $this->template->navigation = View::forge('navigation', $data); 
    }
    
    public function action_index()
    {
            $data['article_count'] = Model_Article::get_total_count();
            $data['article_parsed_count'] = Model_Article::get_parsed_count();
            $data['blog_count'] = Model_Blog::get_count();
            $data['paragraph_count'] = Model_Paragraph::get_total_count();
            $data['word_count'] = Model_Paragraph::get_parsed_spaces_count();
            $this->template->content = View::forge('main/index', $data);
    }       
}
