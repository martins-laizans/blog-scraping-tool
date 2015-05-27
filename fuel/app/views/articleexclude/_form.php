<?php echo Form::open(); ?>

	<fieldset>
		<div class="clearfix">
			<?php echo Form::label('Blogid', 'blogid'); ?>

			<div class="input">
				<?php echo Form::input('blogid', Input::post('blogid', isset($articleexclude) ? $articleexclude->blogid : ''), array('class' => 'span4')); ?>

			</div>
		</div>
		<div class="clearfix">
			<?php echo Form::label('Ruleid', 'ruleid'); ?>

			<div class="input">
				<?php echo Form::input('ruleid', Input::post('ruleid', isset($articleexclude) ? $articleexclude->ruleid : ''), array('class' => 'span4')); ?>

			</div>
		</div>
		<div class="clearfix">
			<?php echo Form::label('Element', 'element'); ?>

			<div class="input">
				<?php echo Form::input('element', Input::post('element', isset($articleexclude) ? $articleexclude->element : ''), array('class' => 'span4')); ?>

			</div>
		</div>
		<div class="actions">
			<?php echo Form::submit('submit', 'Save', array('class' => 'btn btn-primary')); ?>

		</div>
	</fieldset>
<?php echo Form::close(); ?>