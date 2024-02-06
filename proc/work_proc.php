<?
    include "./_common.php";

    $mode = $_POST[mode];

    if($mode == "proc"){
        $idx = $_POST[idx];
        $todo_contents = $_POST[todo_contents];
        $query_tmp = array();

        if($idx){
            $query = " UPDATE $KH[WORK_BOARD] SET
                        WORK_CONTENTS = '$todo_contents',
                        WORK_UPDATE_TIME = '$time' ";
        }else{
            $query = " INSERT INTO $KH[WORK_BOARD] SET
                        WORK_CONTENTS = '$todo_contents',
                        WORK_REG_TIME = '$time',
						WB_MEMBER_IDX = $member[IDX],
						WB_MEMBER_ID = '$member[MEMBER_ID]' ";
        }

        if($result = query($query)){
            $query_tmp[status] = "OK";
        }else{
            $query_tmp[status] = "NOT-FOUND";
        }
    }

    $query = " SELECT * FROM $KH[TODO_LIST] ";
    $result = query($query);

    while($row = mysqli_fetch_assoc($result)){

    }

    echo json_encode($query_tmp, true);
?>