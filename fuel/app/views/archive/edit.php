<h2>Editing Archive</h2>
<br>

<?php echo render('archive/_form'); ?>
<p>
	<?php echo Html::anchor('archive/view/'.$archive->id, 'View'); ?> |
	<?php echo Html::anchor('archive', 'Back'); ?></p>
