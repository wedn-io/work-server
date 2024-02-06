<?
	/*
		업무 관련 게시판, 미확정
		Last update 2024-02-06 IKH
	*/

	if($_GET['idx']) $idx = $_GET['idx'];
	$query = " SELECT * FROM $KH[WORK_BOARD] ";
	$result = query($query);
?>
<input type="hidden" name="idx" value="<?=$idx?>">

<h2>업무</h2>
<textarea name="todo_contents"></textarea>
<button type="button" onClick="todolist_registration()">Add</button>

<script>
	function todolist_registration(){
		let idx = document.getElementsByName("idx")[0].value;
		let todo_contents = document.getElementsByName("todo_contents")[0].value;

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