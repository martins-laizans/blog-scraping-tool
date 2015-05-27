<h2>Viewing #<?php echo $articleexclude->id; ?></h2>

<p>
	<strong>Blogid:</strong>
	<?php echo $articleexclude->blogid; ?></p>
<p>
	<strong>Ruleid:</strong>
	<?php echo $articleexclude->ruleid; ?></p>
<p>
	<strong>Element:</strong>
	<?php echo $articleexclude->element; ?></p>

<?php echo Html::anchor('articleexclude/edit/'.$articleexclude->id, 'Edit'); ?> |
<?php echo Html::anchor('articleexclude', 'Back'); ?>