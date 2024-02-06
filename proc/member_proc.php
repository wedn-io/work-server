<?
	/*
		[TODO] 로그인 관련 보안 체크 및 보완
		회원 프로세스 페이지
		Last update 2024-02-06 IKH
	*/

    include "./_common.php";

    $mode = $_POST[mode];
    $member_id = $_POST[member_id];
    $member_password = $_POST[member_password];
    $query_tmp = array();


    if($mode == "login" || $mode == "join"){
        /* 회원 검색 */
        $query = " SELECT MEMBER_ID, MEMBER_PASSWORD, MEMBER_DEL_TIME FROM $KH[MEMBER]
                    WHERE MEMBER_ID = '$member_id' ";
        $result = query($query);
        $row = mysqli_fetch_assoc($result);
    }

    if($mode == "login"){
        if($row[IDX]){
            $ori_password = $row[MEMBER_PASSWORD];

            /* 패스워드를 검사한다. */
            if(password_verify($member_password, $ori_password) && !$row[MEMBER_DEL_TIME]){
                /* 패스워드 일치시 통과 */
                $_SESSION[S_MEMBER_ID] = $row[MEMBER_ID];
                $query_tmp[status] = "OK";
                $query_tmp[msg] = "Login Complete!";
            }else{
                /* [보안] 아이디, 패스워드 틀리는 문구 통일 */
                $query_tmp[status] = "NOT-FOUND";
                $query_tmp[msg] = "The input is invalid.1";
            }
        }else{
            /* [보안] 아이디, 패스워드 틀리는 문구 통일 */
            $query_tmp[status] = "NOT-FOUND";
            $query_tmp[msg] = "The input is invalid.2".$query;
        }
    }elseif($mode == "join"){
        if($row[IDX]){
            $query_tmp[status] = "NOT-FOUND-ID";
            $query_tmp[msg] = "Duplicate ID.";
        }else{
            /* 패스워드 암호화 */
            $member_password_hash = password_hash($member_password, PASSWORD_DEFAULT);

            /* 회원가입 */
            $query = " INSERT INTO $KH[MEMBER] SET
                        MEMBER_ID = '$member_id',
                        MEMBER_PASSWORD = '$member_password_hash',
                        MEMBER_REG_TIME = '$time' ";
            if($result = query($query)){
                $_SESSION[S_MEMBER_ID] = $row[MEMBER_ID];
                $query_tmp[status] = "OK";
                $query_tmp[msg] = "Join Complete!";
            }else{
                $query_tmp[status] = "NOT-FOUND";
                $query_tmp[msg] = "Error.";
            }
        }
    }

    echo json_encode($query_tmp, true);
?>