<?
	# /work/index.php

	$query = " SELECT * FROM $KH[TODO_LIST] ";
	$result = query($query);
?>

<h2>TODOLIST</h2>
<textarea name="todo-contents"></textarea>
<button type="button">Add</button>

<script>
	function Todolist_Add(){
		let todo_contents = document.getElementsByName("todo-contents");
		console.log(todo_contents);
	}
</script>