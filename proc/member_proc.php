<?
    include "./_common.php";

    $mode = $_POST[mode];

    if($mode == "login"){
        $member_id = $_POST[member_id];
        $member_password = $_POST[member_password];
        $query_tmp = array();

        $query = " SELECT MEMBER_ID, MEMBER_PASSWORD, MEMBER_DEL_TIME FROM $KH[TODO_LIST] 
                    WHERE MEMBER_ID = '$member_id' 
                    AND MEMBER_DEL_TIME IS NULL ";
        $result = query($query);
        $row = mysqli_fetch_assoc($result);

        if($row[IDX]){
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
        
    }

    echo json_encode($query_tmp, true);
?>