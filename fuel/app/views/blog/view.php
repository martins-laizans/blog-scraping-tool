<h2>Viewing #<?php echo $blog->id; ?></h2>

<p>
	<strong>Url:</strong>
	<?php echo $blog->url; ?></p>
<p>
	<strong>Status:</strong>
	<?php echo $blog->status; ?></p>
<p>
	<strong>Crawldate:</strong>
	<?php echo $blog->crawldate; ?></p>

<?php echo Html::anchor('blog/edit/'.$blog->id, 'Edit'); ?> |
<?php echo Html::anchor('blog', 'Back'); ?>