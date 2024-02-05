<? 
	if (!defined('_KH_')){
		alert("개별 접근이 불가능한 페이지입니다.", "/index.php");
	}  
?>
<!DOCYTYPE HTML>
<html>
	<head>
		<title>테스트 페이지</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<link href="/inc/css/default.css" rel="stylesheet" type="text/css" />

		<? # Jquery 항상 실행, 추후 소스 다운로드 ?>
		<script src="https://code.jquery.com/jquery-latest.min.js"></script>

	</head>
	<body>
		<ul>
			<? # 업무 관련 ?>
			<li><a href="<?=LOCATION_PATH($INDEX_PATH, $DEFAULT_DIR, $WORK_DIR)?>">업무관리</a></li>
		</ul>