<?
	/*
		[TODO] DB 관련 보안 체크 및 보완
		DB 연결 페이지
		Last update 2024-02-06 IKH
	*/

	$host = '3.34.190.224';
	$dbname = 'KH_SOLUTION';
	$username = 'root';
	$password = 'wedn060104!';

	$conn = new mysqli($host, $username, $password, $dbname);

	if ($mysqli->connect_errno) {
		echo "connect error : " . $mysqli->connect_error;
		exit();
	}
?>