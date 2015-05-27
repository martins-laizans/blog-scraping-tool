<h2>Listing Blogs</h2>
<br>
<?php echo Html::anchor('blog/create', 'Add new Blog', array('class' => 'btn btn-success')); ?>
<?php if ($blogs): ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Url</th>
			<th>Status</th>
			<th>Crawldate</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($blogs as $blog): ?>		<tr>

			<td><?php echo Html::anchor($blog->url, $blog->url); ?></td>
			<td><?php echo $blog->status; ?></td>
			<td><?php echo $blog->crawldate; ?></td>
			<td>
				<?php echo Html::anchor('blog/view/'.$blog->id, 'View', array('class' => 'btn btn-mini')); ?> | 
				<?php echo Html::anchor('blog/edit/'.$blog->id, 'Edit', array('class' => 'btn btn-mini btn-success')); ?> |
				<?php echo Html::anchor('blog/delete/'.$blog->id, 'Delete', array('onclick' => "return confirm('Are you sure?')", 'class' => 'btn btn-mini btn-danger')); ?> | 
                                <?php echo Html::anchor('archive/auto/'.$blog->id, 'Archive page', array('class' => 'btn btn-mini btn-primary')); ?> |
                                <?php echo Html::anchor('articleexclude/index/'.$blog->id, 'Article', array('class' => 'btn btn-mini btn-info')); ?> |
                                <?php echo Html::anchor('articleexclude/preview/'.$blog->id, 'Preview', array('class' => 'btn btn-mini btn-inverse')); ?>
                                <?php echo Html::anchor('converter/convert/'.$blog->id, 'Convert', array('class' => 'btn btn-mini btn-success')); ?>
			</td>
		</tr>
<?php endforeach; ?>	</tbody>
</table>

<?php else: ?>
<p>No Blogs.</p>

<?php endif; ?><p>
	<?php echo Html::anchor('blog/create', 'Add new Blog', array('class' => 'btn btn-success')); ?>

</p>
