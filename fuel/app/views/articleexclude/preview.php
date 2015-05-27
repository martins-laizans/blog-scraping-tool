<h2>Preview parsing</h2>

<h3>Parse result from article <?php echo Html::anchor($article_url, $article_url, array('target' => "_blank")); ?></h3>   
<p>
    <?php 
        Package::load('simplehtml');
        
        $lib_path = APPPATH.'language_detect\lib\TextLanguageDetect';
        Fuel::load($lib_path.'\TextLanguageDetect.php');
        Fuel::load($lib_path.'\LanguageDetect\TextLanguageDetectParser.php');
        Fuel::load($lib_path.'\LanguageDetect\TextLanguageDetectISO639.php');
        $l = new TextLanguageDetect\TextLanguageDetect;
        $l->setNameMode(2);
        
        $simple_html = new Simplehtml;
        $html = $simple_html->file_get_html($article_url);

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
        
        $article_id = 'TEST_PARSE';
        $html = $html->find($content_element);
        $html = reset($html);
        $file_url = DOCROOT.'/'.$blog_urls[$blog_id].'/';
        $meta_file_url = DOCROOT.'/'.$blog_urls[$blog_id].'/';
        try{
            File::create_dir(DOCROOT.'/', $blog_urls[$blog_id], 7777);
        } catch (Exception $ex) {
            //folder already exists
        }
        
//        echo $file_url;
//        exit();
        File::update($file_url, $article_id.'.txt', ''); //create file
	File::update($meta_file_url, $article_id.'.xml', ''); //create file
        
        $paragraph_nr = 1;
        
        function output($child,$file_url,$article_id,$paragraph_nr, $l)
        {
            $child_text = trim($child->plaintext);
            if($child_text == '')
                return;
            echo $child;

            $text = trim(html_entity_decode($child->plaintext, ENT_NOQUOTES , 'UTF-8'));
            $spaces = substr_count($text, ' ');
            $number_of_words = $spaces+1; //10;
            
            
            $result = $l->detect($text, 4);
            
            $languages = array();
            $counter = 1;
            foreach($result as $lang => $probability):
                $languages['language_'.$counter] = array('language'=>$lang, 'possibility'=>$probability);
                $counter ++;
            endforeach;
            
            $number_of_symbols_in_paragraph = mb_strlen($text);
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
        
        $title = 'test';
        $published = '2014-05-27';
        $paragraphs = array();
        
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

            //insert paragraph
//            if($save_to_db){
//                    $query = "INSERT INTO paragraph VALUES (NULL,'".($text). "',".$article_id.','.strlen($text).','.$spaces.")";
//                    $result = $mysqli->query($query);
//                    $paragraph_id = $mysqli->insert_id;
//            }
            $paragraph_nr++;
        endforeach;
        
        //language of all article
        $result = $l->detect($html->plaintext, 4);
        $languages = array();
        $counter = 1;
        foreach($result as $lang => $probability):
            $languages['language_'.$counter] = array('language'=>$lang, 'possibility'=>$probability);
            $counter ++;
        endforeach;
        
        $meta_array = array('article' => array('title'=>$title, 'published'=>$published ,'languages' => $languages, 'paragraphs' => $paragraphs));
        $xml = Format::forge($meta_array)->to_xml();
        //META
        File::append($meta_file_url, $article_id.'.xml', $xml);
        
        echo Html::anchor('archive/auto/'.$blog_id, 'Archive page', array('class' => 'btn btn-mini btn-primary')); 
        echo "<br>";
        echo Html::anchor('articleexclude/index/'.$blog_id, 'Article', array('class' => 'btn btn-mini btn-info')); 
        echo "<br>";
        echo Html::anchor('article/create/'.$blog_id, 'Find articles', array('class' => 'btn btn-mini btn-warning')); 
    ?>
</p>
