<?
	include "./common.php";
	//error_reporting( E_ALL );
	//ini_set( "display_errors", 1 );

	$INCLUDE_HEAD = LAYOUT_PATH($LAYOUT_INCLUDE_DIR, $HEAD_PATH);
	$INCLUDE_TAIL = LAYOUT_PATH($LAYOUT_INCLUDE_DIR, $TAIL_PATH);

	switch ($DIR) {
		case $LOGIN_DIR: # 로그인
			$INCLUDE_CONTENTS = INCLUDE_PATH($MEMBER_DIR, $LOGIN_PATH);
			break;
		case $JOIN_DIR: # 회원가입
			$INCLUDE_CONTENTS = INCLUDE_PATH($MEMBER_DIR, $JOIN_PATH);
			break;
		case $WORK_DIR: # 업무
			if(!$MODE){
				$INCLUDE_CONTENTS = INCLUDE_PATH($WORK_DIR, $INDEX_PATH);
			}else{
				if($MODE == "list"){
					$INCLUDE_CONTENTS = INCLUDE_PATH($WORK_DIR, $LIST_PATH);
				}elseif($MODE == "write"){
					$INCLUDE_CONTENTS = INCLUDE_PATH($WORK_DIR, $WRITE_PATH);
				}
			}
			break;
		case $WORKLOG_DIR:
			$INCLUDE_CONTENTS = INCLUDE_PATH($WORK_DIR, $WORKLOG_PATH);
			break;
		case $BOARD_DIR:
			$INCLUDE_CONTENTS = INCLUDE_PATH($WORK_DIR, $WORKLOG_PATH);
			break;
		default:
			$INCLUDE_CONTENTS = INCLUDE_PATH($MAIN_DIR, $INDEX_PATH);
			break;
	}

	# 2024-02-05 비로그인 시 로그인 페이지로
	if(!$member && $DIR != $JOIN_DIR){
		$INCLUDE_CONTENTS = INCLUDE_PATH($MEMBER_DIR, $LOGIN_PATH);
	}

	include $INCLUDE_HEAD;
	include $_SERVER['DOCUMENT_ROOT']."/config.php";
	echo $INCLUDE_CONTENTS;
	include $INCLUDE_CONTENTS;
	include $INCLUDE_TAIL;
?>