<?
	# /work/index.php
	$idx = $_GET[idx];
	$query = " SELECT * FROM $KH[TODO_LIST] ";
	$result = query($query);
?>
<input type="hidden" name="idx" value="<?=$idx?>">

<h2>TODOLIST</h2>
<textarea name="todo-contents"></textarea>
<button type="button" onClick="todolist_registration()">Add</button>

<script>
	function todolist_registration(){
		let idx = document.getElementsByName("idx")[0].value;
		let todo_contents = document.getElementsByName("todo-contents")[0].value;

		<? # AJAX 전송 시 POST 고정 ?>
		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: {
				"idx":idx,
				"todo_contents":todo_contents
			},
			url: '/proc/todo_proc.php',
			success:function(res){
				console.log(res.status);
			},
			error:function(){
				console.log(res.status);
			}
		});
	}
</script>