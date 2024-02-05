<?
	/* 경로설정 */

	# 페이지 호출 함수 /index.php?dir=code
	function LOCATION_PATH($INDEX_PATH, $DIR_PATH, $MOVE_PATH){
		return $INDEX_PATH."?".$DIR_PATH."=".$MOVE_PATH;
	}

	# 페이지 include 함수 /work/index.php
	function INCLUDE_PATH($INCLUDE_PATH, $INDEX_PATH){
		return $INCLUDE_PATH.$INDEX_PATH;
	}

	# head.php, tail.php 호출 함수
	function LAYOUT_PATH($LAYOUT_INCLUDE_PATH, $_PATH){
		return $LAYOUT_INCLUDE_PATH."/".$_PATH;
	}

	# 작업 페이지 경로
	function DEV_PATH($REQUEST_INFO){
		$RESPONSE_INFO = "/".$REQUEST_INFO;
		return "Directory ". $RESPONSE_INFO."<br>";
	}

	# 작업 서버 정보
	function DEV_INFO(){
		$SERVER_INFO = $_SERVER['SERVER_SOFTWARE']." ";
		$SERVER_INFO .= $_SERVER['SERVER_NAME'];
		return "Server info ".$SERVER_INFO."<br>";
	}


	# 작업 페이지 파라미터
	function DEV_PARAMETER($POST_PARAM, $GET_PARAM){
		$RESPONSE_INFO = "";

		if($POST_PARAM || $GET_PARAM){
			$RESPONSE_INFO .= "<br>=========================<br>";
			if($POST_PARAM){
				$RESPONSE_INFO .= "[POST]";
				$RESPONSE_INFO .= DEV_PARAMETER_OUTPUT($POST_PARAM);
			}
			if($GET_PARAM){
				$RESPONSE_INFO .= "[GET]";
				$RESPONSE_INFO .= DEV_PARAMETER_OUTPUT($GET_PARAM);
			}
			$RESPONSE_INFO .= "=========================<br>";
		}

		return $RESPONSE_INFO;
	}

	# 파라미터 출력
	function DEV_PARAMETER_OUTPUT($PARAMETER){
		$RESPONSE_INFO = "<br>";

		foreach($PARAMETER as $k => $v){
			$RESPONSE_INFO .= "[". $k . " >>> " . $v . "]<br>";
		}

		return $RESPONSE_INFO;
	}

	/* 개발용 아이피 설정 */
	$ALLOW_IP = [
		'211.184.136.132'
	];

	$_DEV = 0;

	if(in_array($_SERVER['REMOTE_ADDR'], $ALLOW_IP)){
		$_DEV = 1;
	}
	/* */


	/* DEFAULT PATH */
	$HEAD_PATH = "head.php";
	$TAIL_PATH = "tail.php";
	$RELATIVE_INDEX_PATH = "./index.php";
	$INDEX_PATH = "/index.php";

	/* CONTENTS PATH */
	$WORK_PATH = "/work.php";
	$WORKLOG_PATH = "/work_log.php";

	/* DIR PATH */
	$DEFAULT_DIR = "dir";
	$MAIN_DIR = "main";
	$WORK_DIR = "work";
	$WORKLOG_DIR = "work_log";
	$BOARD_DIR = "board";


	/* 파라미터 확인 */
	$DIR = $_GET['dir'];

	/* 임시로 main만 사용 */
	if($DIR){
		//$LAYOUT_INCLUDE_DIR = "sub";
		$LAYOUT_INCLUDE_DIR = "main";
	}else{
		$LAYOUT_INCLUDE_DIR = "main";
	}
	
	$host = '3.34.190.224'; // 데이터베이스 호스트
	$dbname = 'KH_SOLUTION'; // 데이터베이스 이름
	$username = 'root'; // 데이터베이스 사용자 이름
	$password = 'wedn060104!'; // 데이터베이스 비밀번호

	// MySQLi 객체를 생성하여 데이터베이스에 연결
	$mysqli = new mysqli($host, $username, $password, $dbname);

	// 연결 상태 확인
	if ($mysqli->connect_errno) {
		echo "데이터베이스 연결 실패: " . $mysqli->connect_error;
		exit();
	} else {
		echo "데이터베이스 연결 성공";
	}

	// 추가적인 설정 옵션 지정 (옵션)
	$mysqli->set_charset("utf8mb4");
?>