<?
	# /work/index.php

	$query = " SELECT * FROM $KH[TODO_LIST] ";
	$result = query($query);
?>

<h2>TODOLIST</h2>
<textarea name="todo-contents"></textarea>
<button type="button" onClick="todolist_registration()">Add</button>

<script>
	function todolist_registration(){
		alert("@@");
		let todo_contents = document.getElementsByName("todo-contents")[0].value;
		console.log(todo_contents);
	}
</script>