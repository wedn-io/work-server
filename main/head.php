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
		<a href="<?=LOCATION_PATH($INDEX_PATH, $DEFAULT_DIR, $WORK_DIR)?>">TEST</a>
		<a href="<?=LOCATION_PATH($INDEX_PATH, $DEFAULT_DIR, $WORKLOG_DIR)?>">TEST2</a>
		<a href="<?=LOCATION_PATH($INDEX_PATH, $DEFAULT_DIR, $BOARD_DIR)?>">BOARD</a>