<h2>Listing Articles for blog <?php echo $blog_url; ?></h2>
<br>
<?php if ($articles): ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Author</th>
			<th>Url</th>
			<th>Published</th>
			<th>Crawled</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($articles as $article): ?>		<tr>

			<td><?php echo $article->author; ?></td>
			<td><?php echo Html::anchor($article->url, $article->url, array('target' => '_blank')); ?></td>
			<td><?php echo $article->published; ?></td>
			<td><?php echo $article->crawled; ?></td>
			<td>
				<?php // echo Html::anchor('article/view/'.$article->id, 'View'); ?> 
				<?php // echo Html::anchor('article/edit/'.$article->id, 'Edit'); ?> 
				<?php echo Html::anchor('article/delete/'.$article->id, 'Delete', array('onclick' => "return confirm('Are you sure?')")); ?> 
                                <?php echo Html::anchor('article/parse/1/'.$blog_id.'/'.$article->id, 'Parse', array('class' => 'btn btn-primary')); ?>

			</td>
		</tr>
<?php endforeach; ?>	</tbody>
</table>

<?php else: ?>
<p>No Articles.</p>

<?php endif; ?><p>
	<?php // echo Html::anchor('article/create', 'Add new Article', array('class' => 'btn btn-success')); ?>

</p>
