<?
	/*
		공통 코드 설정
		Last update 2024-02-06 IKH
	*/

	session_start();

	# 테이블 정보
	$KH = array();
	$KH[WORK_BOARD] = "KH_WORK_BOARD";
	$KH[TODO_LIST] = "KH_TODO_LIST";
	$KH[MEMBER] = "KH_MEMBER";

	# 경로 정보
	$KH_PATH[ROOT_PATH] = $_SERVER['DOCUMENT_ROOT'];

	# DB 연결
	/*
		[TODO] DB보안 관련 체크 및 보완
	*/
	include $KH_PATH[ROOT_PATH]."/common/dbconn.php";

	# 공통 함수
	include $KH_PATH[ROOT_PATH]."/lib/common.lib.php";

	# 개발자 모드 아이피 설정
	$ALLOW_IP = [
		'211.184.136.132'
	];

	$_DEV = 0;
	if(in_array($_SERVER['REMOTE_ADDR'], $ALLOW_IP)) $_DEV = 1;


	/* CONFIG PATH */
	$INCLUDE_CONFIG = "/config.php";

	/* DEFAULT PATH */
	$HEAD_PATH = "head.php";
	$TAIL_PATH = "tail.php";
	$RELATIVE_INDEX_PATH = "./index.php";
	$INDEX_PATH = "/index.php";

	/* CONTENTS PATH */
	$WORK_PATH = "/work.php";
	$WORKLOG_PATH = "/work_log.php";
	$LOGIN_PATH = "/login.php";
	$JOIN_PATH = "/join.php";

	/* DIR PATH */
	$DEFAULT_DIR = "dir";
	$MAIN_DIR = "main";
	$WORK_DIR = "work";
	$WORKLOG_DIR = "work_log";
	$BOARD_DIR = "board";
	$MEMBER_DIR = "member";
	$LOGIN_DIR = "login";
	$JOIN_DIR = "join";

	/* 경로 파라미터 */
	$DIR = $_GET['dir'];

	/*
		[TODO] 관리자 레이아웃, 서브 레이아웃 설정 및 추가
		메인만 사용 (임시)
	*/
	if($DIR){
		//$LAYOUT_INCLUDE_DIR = "sub";
		$LAYOUT_INCLUDE_DIR = "main";
	}else{
		$LAYOUT_INCLUDE_DIR = "main";
	}

	/**********************************************************/



	# 시간 정보
	$time = time();

	# 회원 정보
	$is_member = 0;
	$is_admin = 0;

	$member = array();
	$query = " SELECT * FROM $KH[MEMBER] WHERE MEMBER_ID = '$_SESSION[S_MEMBER_ID]' ";
	$result = query($query);
	$member = mysqli_fetch_assoc($result);

	if($member[IDX]) $is_member = 1;
	if($member[LEVEL] == 10) $is_admin = 1;
?>