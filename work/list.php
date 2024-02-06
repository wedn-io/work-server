<?
	/*
		업무 게시판, 미확정
		Last update 2024-02-06 IKH
	*/

	if($_GET['sfl']) $stx = $_GET['sfl'];
	if($_GET['stx']) $stx = $_GET['stx'];

	# 게시판 데이터
	$result = getPageData($KH['WORK_BOARD'], $currentPage, $perPage);
	$data = $result['data'];
	$totalPages = $result['totalPages'];

	$qstr = $KH_BOARD['PARAMETER'];

	# 페이징 버튼
	$pageLinks = getPageLinks($currentPage, $totalPages, $qstr);
?>
<h2>업무</h2>


<form>
	<input type="hidden" name="dir" value="<?=$DIR?>">
	<input type="hidden" name="mode" value="<?=$MODE?>">
	<input type="hidden" name="page" value="<?=$PAGE?>">
	<select name="sfl">
		<option value="WB_BOARD_IDX" <?=$sfl=="WB_BOARD_IDX" ? "selected" : ""?>>업무번호</option>
		<option value="WB_TITLE" <?=$sfl=="WB_TITLE" ? "selected" : ""?>>제목</option>
		<option value="WB_CONTENTS" <?=$sfl=="WB_CONTENTS" ? "selected" : ""?>>내용</option>
	</select>
	<input type="text" name="stx" placeholder="검색어를 입력하세요." value="<?=$stx?>">
	<button type="button" onClick="search()">검색</button>
</form>
<div>
	<a href="<?=LOCATION_PATH($INDEX_PATH, $DEFAULT_DIR, $WORK_DIR)?>&mode=write">업무등록</a>
</div>
<?=$pageLinks;?>



<script>
	function search(){
		let stx = document.getElementsByName("stx")[0].value;
	}
</script>
