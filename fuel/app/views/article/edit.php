<h2>Editing Article</h2>
<br>

<?php echo render('article/_form'); ?>
<p>
	<?php echo Html::anchor('article/view/'.$article->id, 'View'); ?> |
	<?php echo Html::anchor('article', 'Back'); ?></p>
