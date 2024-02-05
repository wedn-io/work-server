<?
	# /work/index.php

	$query = " SELECT * FROM $KH[TODO_LIST] ";
	$result = query($query);
?>

<h2>TODOLIST</h2>
<textarea name="todo_contents"></textarea>
<button type="button">Add</button>