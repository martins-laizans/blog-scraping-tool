<h2>Article count per blog</h2>
<br>
<?php if ($article_count): ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Blog</th>
			<th>Article count</th>
			<th>Parsed count</th>
                        <th>Ready for parsing</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($article_count as $blog_id => $count): ?>		
            <tr>
                    <td><?php echo $blog_urls[$blog_id]; ?></td>
                    <td><?php echo $count['total']; ?></td>
                    <td><?php echo $count['parsed']; ?></td>
                    <td><?php echo $count['to_parse']; ?></td>
                    <td><?php echo Html::anchor('article/create/'.$blog_id, 'Search for new articles', array('class' => 'btn btn-success')); ?></td>
                    <td><?php echo Html::anchor('article/blogarticles/'.$blog_id, 'View articles', array('class' => 'btn')); ?></td>
                    <td><?php echo Html::anchor('article/parse/100/'.$blog_id, 'P 100', array('class' => 'btn btn-warning')); ?></td>
            </tr>
<?php endforeach; ?>	</tbody>
</table>


<?php echo Form::open(); ?>

	<fieldset>
		<div class="clearfix">
			<?php echo Form::label('Parse count', 'parse_count'); ?>

			<div class="input">
				<?php echo Form::input('parse_count', 5, array('class' => 'span4')); ?>
			</div>
		</div>
		<div class="actions">
			<?php echo Form::submit('submit', 'Parse', array('class' => 'btn btn-primary')); ?>

		</div>
	</fieldset>
<?php echo Form::close(); ?>

<?php else: ?>
<p>No Articles.</p>

<?php endif; ?><p>
	<?php // echo Html::anchor('article/create', 'Add new Article', array('class' => 'btn btn-success')); ?>

</p>
