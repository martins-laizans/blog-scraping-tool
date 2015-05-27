<h2>Viewing #<?php echo $article->id; ?></h2>

<p>
	<strong>Blogid:</strong>
	<?php echo $article->blogid; ?></p>
<p>
	<strong>Author:</strong>
	<?php echo $article->author; ?></p>
<p>
	<strong>Url:</strong>
	<?php echo $article->url; ?></p>
<p>
	<strong>Published:</strong>
	<?php echo $article->published; ?></p>
<p>
	<strong>Crawled:</strong>
	<?php echo $article->crawled; ?></p>

<?php echo Html::anchor('article/edit/'.$article->id, 'Edit'); ?> |
<?php echo Html::anchor('article', 'Back'); ?>