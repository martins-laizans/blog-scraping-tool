<h2><?php echo $archive_response['title']; ?></h2>
<?php echo Form::open(); 
var_dump($archive_response);
?>

	<fieldset>
                <?php foreach($archive_response['choose'] as $choiceelement => $link): ?>
                <div class="clearfix">
                    <?php echo Form::label($link, $choiceelement); ?>
                    <div class="input">
                        <?php echo Form::radio('choice', $choiceelement); ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <br>
                <div class="clearfix">
			<?php // echo Form::label('Blogid', 'blogid'); ?>

			<div class="input">
				<?php echo Form::hidden('blogid', $archive_response['blogid'], array('class' => 'span4')); ?>
			</div>
		</div>
                <div class="clearfix">
			<?php echo Form::label('Elementid', 'elementid'); ?>

			<div class="input">
				<?php echo Form::hidden('elementid', $archive_response['elementid'], array('class' => 'span4')); ?>
			</div>
		</div>
                
		<div class="actions">
			<?php echo Form::submit('submit', 'Save', array('class' => 'btn btn-primary')); ?>

		</div>
	</fieldset>
<?php echo Form::close(); ?>