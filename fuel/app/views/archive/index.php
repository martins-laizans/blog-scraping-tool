<h2>Listing Archives</h2>
<br>
<?php if ($archives): ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Blogid</th>
			<th>Elementid</th>
			<th>Element</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($archives as $archive): ?>		
            <?php if(key_exists($archive->blogid, $blog_urls)): ?>
                <tr>
                    
			<td><?php echo $blog_urls[$archive->blogid]; ?></td>
			<td><?php echo $element_ids[$archive->elementid]; ?></td>
			<td><?php echo $archive->element; ?></td>
			<td>
				<?php echo Html::anchor('archive/view/'.$archive->id, 'View'); ?> |
				<?php echo Html::anchor('archive/edit/'.$archive->id, 'Edit'); ?> |
				<?php echo Html::anchor('archive/delete/'.$archive->id, 'Delete', array('onclick' => "return confirm('Are you sure?')")); ?>

			</td>
		</tr>
                <?php endif; ?>
<?php endforeach; ?>	</tbody>
</table>

<?php else: ?>
<p>No Archives.</p>

<?php endif; ?><p>
	<?php echo Html::anchor('archive/create', 'Add new Archive', array('class' => 'btn btn-success')); ?>

</p>
