<?
	/*
		업무 게시판 글쓰기, 미확정
		Last update 2024-02-06 IKH
	*/

	if($_GET['idx']){
		$title = "업무수정";
		$IDX = $_GET['idx'];
		$result = query($conn, " SELECT * FROM $KH[WORK_BOARD] WHERE IDX = $IDX ");
		$row = mysqli_fetch_assoc($result);
	}else{
		$title = "업무등록";
	}
?>

<h2>업무등록</h2>

<form name="write_form">
	<select name="WB_STATUS">
		<option value="0" <?=$row['WB_STATUS']=="0" ? "selected" : ""?>>대기</option>
		<option value="1" <?=$row['WB_STATUS']=="1" ? "selected" : ""?>>진행중</option>
		<option value="2" <?=$row['WB_STATUS']=="2" ? "selected" : ""?>>완료</option>
	</select>
	<input type="text" name="WB_BOARD_IDX" placeholder="업무번호" value="<?=$row['WB_BOARD_IDX']?>">
	<textarea name="WB_CONTENTS"><?=$row['WB_CONTENTS']?></textarea>
	<button type="button" onClick="search()">검색</button>
</form>



<script>
	function search(){
		let stx = document.getElementsByName("stx")[0].value;
	}
</script>
