<?php

class Controller_Converter extends Controller_Template
{
         public function before() {
            parent::before();    
            //send page name to navigation to mark active page button
            $this->template->page = $data['page'] = 'converter';
            $this->template->navigation = View::forge('navigation', $data); 
        }
        
	public function action_index()
	{
		$this->template->title = 'Converter &raquo; Index';
		$this->template->content = View::forge('converter/index');
	}
        
	public function action_convert($blog_id)
	{
                $blog = Model_Blog::find($blog_id);
                
                $blog->status = "2";
                $blog->save();
                
                $blog_urls = Model_Blog::urls();
                $blog_url = $blog_urls[$blog_id];
                $folder_url = DOCROOT.'/'.$blog_urls[$blog_id].'/';
                $file_list = scandir($folder_url);
                
                $text_files = array();
                foreach ($file_list as $file):
                    if( strpos($file, 'comment') === FALSE and strpos($file, 'xml') === FALSE)
                           $text_files[] = $file; 
                endforeach;
                
                echo sizeof($text_files);
                for ($i = 2; $i < sizeof($text_files); $i++)
                //for ($i = 2; $i < 3; $i++)
                {
                    echo $text_files[$i].'<br>';
                    $file_parts = explode(".", $text_files[$i]);
                    $article_id = $file_parts[0];
                    $article = Model_Article::find($article_id);
                    
                    if($article)
						$author = $article->author;
                    
					// if there is only one author override with setting from blog
                    if($blog->author != NULL)
                        $author = $blog->author;
                    
                    $url = $article->url;
                    $published = date('j/n/Y', strtotime($article->published));
                    if($published == '1/1/1970')
                        $published = "";
                    $title = $article->title;
                    $author_gender = '';
                    if($blog->author_gender != NULL)
                        $author_gender = $blog->author_gender;
                    
                    $body = '';
                    $myfile = fopen($folder_url.$article_id.'.txt', "r") or die("Unable to open file!");
                    // Process one line at a time until end-of-file
                    $ln_nr = 0;
                    $meta = '<doc title="'.$title.'" source="'.$url.'" author="'.$author.'" authorgender="'.$author_gender.'" published="'.$published.'" genre="Emuāri" keywords="" fileref="p'.$article_id.'">';
                    File::update($folder_url, 'c_'.$article_id.'.txt', ''); //create file
                    File::append($folder_url, 'c_'.$article_id.'.txt', $meta."\r\n");
                    
                    while(!feof($myfile)) {
                        $ln_nr++;
                        $line_from_file = fgets($myfile);
                        if($ln_nr == 1 and $blog->title_in_body_text == 1)
                            continue;
                        $line_from_file = ltrim($line_from_file, '0123456789[]');
                        $line_from_file = trim($line_from_file, ' ');
                        $line_from_file = str_replace ("[bilde]" , "" , $line_from_file); //in case this is needed !!!!
                        
                        if(trim($line_from_file) !== '')
                            File::append($folder_url, 'c_'.$article_id.'.txt', $line_from_file."\r\n"); //this in addition to paragraph
                    }
                    fclose($myfile);
                                        
                    File::append($folder_url, 'c_'.$article_id.'.txt', '</doc>');
				}
	
                
		Session::set_flash('success', 'Converted texts for blog '.$blog->url.'.'. sizeof($text_files));
		Response::redirect('blog');
	}
        
        public function action_inDictionaryCheck()
        {
			
            $dictionary_url = DOCROOT.'/'.'dictionary/lemmas-no-morfologjijas.txt';
            $myfile = fopen($dictionary_url, "r") or die("Unable to open file!");
            $dictionary = array();
            
            //load dictionary in memory
            while(!feof($myfile)) {	
                    $line_from_file = fgets($myfile);
					//echo '"'.rtrim($line_from_file). '"'.'<br>';
                    $dictionary[rtrim($line_from_file)] = 0;
            } 
            fclose($myfile);

            $vertical_format_corpus_text_url = DOCROOT.'/'.'dictionary/vertikalais formats korpusam.txt';
            $myfile = fopen($vertical_format_corpus_text_url, "r") or die("Unable to open file!");
            $word_count_in_article = 0;
			$article_words_in_dictionary = 0;
			$article_words_NOT_in_dictionary = 0;
			
			$previous_article_id = 0;
			$article_out_of_vocab_folder_url = DOCROOT.'/'.'/dictionary/analysis_results';
			File::update($article_out_of_vocab_folder_url, 'outOfVocabulary.txt', ''); //create file
			File::update($article_out_of_vocab_folder_url, 'token_issues.txt', ''); //create file
			File::update($article_out_of_vocab_folder_url, 'statistics_per_article.txt', ''); //create file
            while(!feof($myfile)) {	
                    $line_from_file = stream_get_line($myfile,1000000, "\n");
					
                    /* if(trim($line_from_file) == '' or sizeof(trim($line_from_file) < 2))
                        continue */;
                    //echo '"'.$line_from_file.'"'. sizeof(trim($line_from_file)).'<br>';
                    
                    
					$split_up = explode(" ", $line_from_file);
					if(sizeof($split_up)==1)
						$split_up = explode("	", $line_from_file);
					
					
					if(sizeof($split_up) > 7)
					{
						var_dump($split_up);
						// meta line
						$article_id = substr($split_up[sizeof($split_up)-1],9);
						$article_id = rtrim($article_id, '">');
						if(trim($article_id) == '')
							continue;
						if($previous_article_id  != $article_id)
						{
							//var_dump($split_up);
							//exit();
							//save info about previous article in db
							echo $word_count_in_article. ' '. $article_words_in_dictionary. ' '.$article_words_NOT_in_dictionary.'<br>'; 
							File::append($article_out_of_vocab_folder_url, 'statistics_per_article.txt', $previous_article_id.', '.$word_count_in_article.', '. $article_words_in_dictionary. ', '. $article_words_NOT_in_dictionary. "\r\n");
						}
						$previous_article_id = $article_id;
						echo '-------------- $article_id '.$article_id.' <br>';
						$word_count_in_article = 0;
						$article_words_in_dictionary = 0;
						$article_words_NOT_in_dictionary = 0;
						
						continue;
					}
					if((sizeof($split_up)) == 1)
						continue;
						
					$word_count_in_article++;
                    if(sizeof($split_up) == 3)
					{
						//echo 'from dictionary = ' . $dictionary[$split_up[2]];
						if(array_key_exists($split_up[2], $dictionary))
							//echo "in dictionary " . $split_up[2]. '<br>';
							$article_words_in_dictionary++;
						else
						{
							//echo "NOT in dictionary " . $split_up[2]. '<br>';
							File::append($article_out_of_vocab_folder_url, 'outOfVocabulary.txt', $article_id. ', '. $split_up[2]."\r\n");
							$article_words_NOT_in_dictionary++;
						}
					}
                    else
					{
						// many words instead of one token
                        echo 'size is '.sizeof($split_up);
						File::append($article_out_of_vocab_folder_url, 'token_issues.txt', $article_id. ', '. $line_from_file."\r\n");
					}
            } 
			
			echo $word_count_in_article. ' '. $article_words_in_dictionary. ' '.$article_words_NOT_in_dictionary.'<br>'; 
			File::append($article_out_of_vocab_folder_url, 'statistics_per_article.txt', $previous_article_id.', '.$word_count_in_article.', '. $article_words_in_dictionary. ', '. $article_words_NOT_in_dictionary. "\r\n");
            fclose($myfile);
        }
}
