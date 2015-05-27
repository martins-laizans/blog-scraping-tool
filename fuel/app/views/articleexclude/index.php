<h2>Listing Article elements</h2>
<br>

<?php 
        echo Html::anchor('archive/auto/'.$blog_id, 'Archive page', array('class' => 'btn btn-mini btn-primary')); 
        echo Html::anchor('articleexclude/preview/'.$blog_id, 'Preview', array('class' => 'btn btn-mini btn-inverse'));
?>
<?php if ($articleexcludes): ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Blog</th>
			<th>Rule</th>
			<th>Element</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($articleexcludes as $articleexclude): ?>		
    <tr>

            <td>
                <?php echo $blog_urls[$articleexclude->blogid]; ?>
            </td>
            <td>
                <?php echo $rules[$articleexclude->ruleid]; ?>
            </td>
            <td class="<?php if($articleexclude->ruleid == 1) echo 'ids'; elseif($articleexclude->ruleid == 2) echo 'classes'; ?>">
                <?php echo $articleexclude->element; ?>
            </td>
            <td>
                    <?php echo Html::anchor('articleexclude/view/'.$articleexclude->id, 'View'); ?> |
                    <?php echo Html::anchor('articleexclude/edit/'.$articleexclude->id, 'Edit'); ?> |
                    <?php echo Html::anchor('articleexclude/delete/'.$articleexclude->id, 'Delete', array('onclick' => "return confirm('Are you sure?')")); ?>

            </td>
    </tr>
<?php endforeach; ?>	</tbody>
</table>

<?php else: ?>
    <p>No Articleexcludes.</p>
<?php endif; ?><p>

<h3>Parse result from article <?php echo Html::anchor($article_url, $article_url, array('target' => "_blank")); ?></h3>
<div id="selected_item_options">
			<p><b>Selected:</b> <span id="selected_element"> </span></p>
			<p><b>Element type:</b> <span id="selected_element_type"> </span></p>
			<b>What to do with this?</b>
			<select id="what-to-do">
			  <option value="remove">Remove</option>
			  <option value="add">Add</option>
			  <option value="author">Author</option>
			  <option value="published">Published</option>
                          <option value="content">Content</option>
                          <option value="comment_author">Comment_author</option>
                          <option value="comment_content">Comment_content</option>
			</select>
                        <p>Offset<input id="offset"></input></p>
                        <p>Length<input id="length"></input></p>
			<div>
				<p id="do-it" class="button">Do it</p>
			</div>
                        <div>
				<p id="cancel" class="button">Cancel</p>
			</div>
</div>        
	<?php 
        function visual_markup($tag)
	{
		switch($tag)
		{
			case 'br':
			case 'b':
			case 'script':
			case 'strong': return true;
			default: return false;
		}
	}
        
        function htmlTree($element,$filtered_ids, $filtered_classes){
//                echo $element;
		if(($element and visual_markup($element->tag)) or !$element)
			return;
		if(!$element->plaintext)
			return;
		$id = $element->getAttribute('id');
		$show_id = '';
		if($id)
			$show_id = '<a class="id" href="###">#'.$id.'</a>';
		$class = $element->getAttribute('class');
		
		$is_taken_out = '';
		if($id and array_key_exists($id,$filtered_ids) and $filtered_ids[$id])
			$is_taken_out = 'class="taken-out"';
		
		if($class)
		{
			$class_arr = explode(' ', $class);
			$class = '';
			foreach($class_arr as $one_class)
			{
				if(array_key_exists($one_class,$filtered_classes) and $filtered_classes[$one_class])
					$is_taken_out = 'class="taken-out"';
				$class .= '<a class="class" href="###">.'.$one_class.'</a> ';
			}
		}
		$tag = '<span class="tag">'.$element->tag.'</span>';
		
		
		$str = '<ul><li '.$is_taken_out.'>'.$tag.' '. $show_id.' '.$class.' '.$element->plaintext;
		if($element->children)
		{
			$child = $element->first_child();
			while($child)
			{
				$str .= htmlTree($child,$filtered_ids, $filtered_classes);
				$child = $child->next_sibling();
			}
		}
		$str .= "</li></ul>";
		return $str;
	}
//        var_dump($post);
	echo htmlTree($post,$filtered_ids,$filtered_classes); 
        echo htmlTree($comment,$filtered_ids,$filtered_classes); 
        
        ?>
</p>

<?php 

    echo Html::anchor('archive/auto/'.$blog_id, 'Archive page', array('class' => 'btn btn-mini btn-primary')); 
    echo Html::anchor('articleexclude/preview/'.$blog_id, 'Preview', array('class' => 'btn btn-mini btn-inverse'));
?>
<script type="text/javascript">
	$(document).ready(function(){ 
		$('#selected_item_options').hide();
		function show_options()
		{
			$('#selected_item_options').css("top", ($(window).height() / 2) - ($('#selected_item_options').outerHeight() / 2));
			$('#selected_item_options').css("left", ($(window).width() / 2) - ($('#selected_item_options').outerWidth() / 2));
			$('#selected_item_options').show();
		}
		$(".id" ).click(function(e) {
			$('#selected_element').text($(this).text().substr(1));
			$('#selected_element_type').text('id');
			show_options();
		});
		
		$(".class" ).click(function(e) {
			
			$('#selected_element').text($(this).text().substr(1));
			$('#selected_element_type').text('class');
			show_options();
		});
		
		function send_data_to_server(element, type, action)
		{
			var blog_id = <?php echo $blog_id; ?>;
                        var offset = $('#offset').val();
                        var length = $('#length').val();
                        
			$.ajax({
			url: '<?php echo \Fuel\Core\Uri::base().'articleexclude/create'; ?>',
			type: "post",
			data: {element: element, type:type, action:action, blog_id:blog_id, offset:offset, length:length},
			success: function(e){
                                var response = JSON.parse(e);
				alert("success " + response.status);
				$("#result").html('Submitted successfully');
			},
			error:function(){
				alert("failure");
				$("#result").html('There is error while submit');
			}
		});
		}
		
		$('#do-it').click(function(){
			var selected_elem = $('#selected_element').text();
			var selected_type = $('#selected_element_type').text();
			var todo = $('#what-to-do option:selected').text();
			send_data_to_server(selected_elem, selected_type, todo);
			// alert(selected_elem + ' ' + selected_type + ' ' + todo);
			$('#selected_item_options').hide();
		});
                $('#cancel').click(function(){
			$('#selected_item_options').hide();
		});
		
	});
</script>