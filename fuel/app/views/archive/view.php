<h2>Viewing #<?php echo $archive->id; ?></h2>

<p>
	<strong>Blogid:</strong>
	<?php echo $archive->blogid; ?></p>
<p>
	<strong>Elementid:</strong>
	<?php echo $archive->elementid; ?></p>
<p>
	<strong>Element:</strong>
	<?php echo $archive->element; ?></p>

<?php echo Html::anchor('archive/edit/'.$archive->id, 'Edit'); ?> |
<?php echo Html::anchor('archive', 'Back'); ?>