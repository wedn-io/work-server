<?
	/*
		공통 함수 페이지
		Last update 2024-02-06
	*/

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
		if($PARAMETER){
			$RESPONSE_INFO = "<br>";

			foreach($PARAMETER as $k => $v){
				$RESPONSE_INFO .= "[". $k . " : " . $v . "]<br>";
			}
		}else{
			$PARAMETER = "";
		}

		return $RESPONSE_INFO;
	}

	# 세션 정보 출력
	function DEV_SESSION(){
		if($_SESSION){
			$RESPONSE_SESSION = "[SESSION]";
			$RESPONSE_SESSION .= "<br>";

			foreach($_SESSION as $k => $v){
				$RESPONSE_SESSION .= "[". $k . " : " . $v . "]<br>";
			}

			$RESPONSE_SESSION .= "=========================<br>";
		}else{
			$RESPONSE_SESSION = "";
		}

		return $RESPONSE_SESSION;
	}

	# 메시지 출력
	function alert($msg, $url){
		if($msg) echo "<script>alert('$msg')</script>";
		if($url) echo "location.href='$url'</script>";
		exit;
	}

	/**********************************************************/

	function query($query){
		global $conn;
		$result = mysqli_query($conn, $query);
		return $result;
	}

	/**********************************************************/
?>