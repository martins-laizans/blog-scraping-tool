<?php echo Form::open(); ?>

	<fieldset>
		<div class="clearfix">
			<?php echo Form::label('Blogid', 'blogid'); ?>

			<div class="input">
				<?php echo Form::input('blogid', Input::post('blogid', isset($archive) ? $archive->blogid : ''), array('class' => 'span4')); ?>

			</div>
		</div>
		<div class="clearfix">
			<?php echo Form::label('Elementid', 'elementid'); ?>

			<div class="input">
				<?php echo Form::input('elementid', Input::post('elementid', isset($archive) ? $archive->elementid : ''), array('class' => 'span4')); ?>

			</div>
		</div>
		<div class="clearfix">
			<?php echo Form::label('Element', 'element'); ?>

			<div class="input">
				<?php echo Form::input('element', Input::post('element', isset($archive) ? $archive->element : ''), array('class' => 'span4')); ?>

			</div>
		</div>
		<div class="actions">
			<?php echo Form::submit('submit', 'Save', array('class' => 'btn btn-primary')); ?>

		</div>
	</fieldset>
<?php echo Form::close(); ?>