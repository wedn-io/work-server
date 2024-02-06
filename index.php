<?
	include "./common.php";

	$INCLUDE_HEAD = LAYOUT_PATH($LAYOUT_INCLUDE_DIR, $HEAD_PATH);
	$INCLUDE_TAIL = LAYOUT_PATH($LAYOUT_INCLUDE_DIR, $TAIL_PATH);

	switch ($DIR) {
		case $LOGIN_DIR: # 로그인
			$INCLUDE_CONTENTS = INCLUDE_PATH($MEMBER_DIR, $LOGIN_PATH);
			break;
		case $JOIN_DIR: # 회원가입
			$INCLUDE_CONTENTS = INCLUDE_PATH($MEMBER_DIR, $JOIN_PATH);
			break;
		case $WORK_DIR:
			$INCLUDE_CONTENTS = INCLUDE_PATH($WORK_DIR, $INDEX_PATH);
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

	/* 패스워드 암호화 */
	$member_password = "wedn060104!";
	$member_password_hash = password_hash($member_password, PASSWORD_DEFAULT);

	/* 회원가입 */
	$query = " UPDATE $KH[MEMBER] SET
			MEMBER_PASSWORD = '$member_password_hash',
			WHERE MEMBER_ID = 'master' ";
	query($query);

	include $INCLUDE_HEAD;
	include $_SERVER['DOCUMENT_ROOT']."/config.php";
	include $INCLUDE_CONTENTS;
	include $INCLUDE_TAIL;
?>