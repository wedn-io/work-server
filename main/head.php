<!DOCYTYPE HTML>
<html>
	<head>
		<title>테스트 페이지</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<link href="/inc/css/default.css" rel="stylesheet" type="text/css" />
	</head>

	<? include "./_common.php"; ?>
	<body>
		
		
		
		<ul>
			<? # 업무 관련 ?>
			<li><a href="<?=LOCATION_PATH($INDEX_PATH, $DEFAULT_DIR, $WORK_DIR)?>">업무</a></li>
			<li><a href="<?=LOCATION_PATH($INDEX_PATH, $DEFAULT_DIR, $WORK_DIR)?>">업체관리</a></li>
			<li><a href="<?=LOCATION_PATH($INDEX_PATH, $DEFAULT_DIR, $WORK_DIR)?>">프로젝트관리</a></li>
			<li><a href="<?=LOCATION_PATH($INDEX_PATH, $DEFAULT_DIR, $WORK_DIR)?>">개발문의</a></li>
			<li><a href="<?=LOCATION_PATH($INDEX_PATH, $DEFAULT_DIR, $WORK_DIR)?>">오류문의</a></li>
			<li><a href="<?=LOCATION_PATH($INDEX_PATH, $DEFAULT_DIR, $WORKLOG_DIR)?>">업무기록</a></li>

			<li><a href="<?=LOCATION_PATH($INDEX_PATH, $DEFAULT_DIR, $BOARD_DIR)?>">게시판</a></li>
			<li><a href="<?=LOCATION_PATH($INDEX_PATH, $DEFAULT_DIR, $BOARD_DIR)?>">출입기록</a></li>
		</ul>