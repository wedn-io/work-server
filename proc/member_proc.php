<?
    include "./_common.php";

    $mode = $_POST[mode];
    $member_id = $_POST[member_id];
    $member_password = $_POST[member_password];
    $query_tmp = array();
    
    
    if($mode == "login" || $mode == "join"){
        # 회원 검색 쿼리
        $query = " SELECT MEMBER_ID, MEMBER_PASSWORD, MEMBER_DEL_TIME FROM $KH[MEMBER] 
                    WHERE MEMBER_ID = '$member_id' ";
        $result = query($query);
        $row = mysqli_fetch_assoc($result);
    }

    if($mode == "login"){
        if($row[IDX]){  

            if($row[MEMBER_DEL_TIME]){
                # 탈퇴 회원 로그인 불가 처리
                $query_tmp[status] = "NOT-FOUND";
                $query_tmp[msg] = "The input is invalid.";

                echo json_encode($query_tmp, true);
                exit;
            }

            $ori_password = $row[MEMBER_PASSWORD];

            # 패스워드를 검사한다.
            if(password_verify($member_password, $ori_password)){
                # 패스워드 일치시 통과
                $_SESSION[S_MEMBER_ID] = $row[MEMBER_ID];
                $query_tmp[status] = "OK";
                $query_tmp[msg] = "Login Complete!";
            }else{
                # [보안] 아이디, 패스워드 틀리는 문구 통일
                $query_tmp[status] = "NOT-FOUND";
                $query_tmp[msg] = "The input is invalid.";
            }
        }else{
            # [보안] 아이디, 패스워드 틀리는 문구 통일
            $query_tmp[status] = "NOT-FOUND";
            $query_tmp[msg] = "The input is invalid.";
        }
    }elseif($mode == "join"){
        if($row[IDX]){
            $query_tmp[status] = "NOT-FOUND-ID";
            $query_tmp[msg] = "Duplicate ID.";
        }else{
            # 패스워드 암호화
            $member_password_hash = password_hash($member_password, PASSWORD_DEFAULT);

            # 회원가입
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