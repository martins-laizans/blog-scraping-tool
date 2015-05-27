<h2>Editing Blog</h2>
<br>

<?php echo render('blog/_form'); ?>
<p>
	<?php echo Html::anchor('blog/view/'.$blog->id, 'View'); ?> |
	<?php echo Html::anchor('blog', 'Back'); ?></p>
