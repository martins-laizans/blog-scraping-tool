<h2>Results of checking page <?php echo $blog_url ?></h2>


<?php echo html_entity_decode($archive_response); ?> 
<?php
        echo Html::anchor('articleexclude/index/'.$blogid, 'Article', array('class' => 'btn btn-mini btn-info')); 
        echo "<br>";
        echo Html::anchor('articleexclude/preview/'.$blogid, 'Preview', array('class' => 'btn btn-mini btn-inverse'));
        echo "<br>";
        echo Html::anchor('archive', 'Archive pages', array('class' => 'btn btn-mini btn-primary')); 
?>