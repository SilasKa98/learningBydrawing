<?php
include 'db_connector.php';
if($_POST["method"] == "learningResults"){
    $tested_value = $_POST["data"];
    $result = $_POST["result"];
    $category = $_POST["category"];
    $uuid = $_POST["uuid"];
    if($result == 0){
        $right_answer = 0;
        $wrong_answer = 1;
    }else{
        $right_answer = 1;
        $wrong_answer = 0;
    }

    $sql = "select * from learningresults where uuid=? and tested_value=? and category=?;";
    $stmt = mysqli_stmt_init($connection);
    if(!mysqli_stmt_prepare($stmt, $sql)){
    echo "SQL Statement failed";
    }else{
        mysqli_stmt_bind_param($stmt, "sss", $uuid, $tested_value, $category);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result->num_rows == 0) {
            $sql3 ="insert into learningresults (uuid,category,tested_value,right_answers,wrong_answers) values (?,?,?,?,?);";
            $stmt3 = mysqli_stmt_init($connection);
            if(!mysqli_stmt_prepare($stmt3, $sql3)){
                echo "SQL error1";
            }else{
                mysqli_stmt_bind_param($stmt3, "sssii", $uuid, $category, $tested_value, $right_answer, $wrong_answer);
                mysqli_stmt_execute($stmt3);
            }
        }else{
            $sql3 ="update learningresults set right_answers=right_answers+?, wrong_answers=wrong_answers+? where uuid=? and category=? and tested_value=?;";
            $stmt3 = mysqli_stmt_init($connection);
            if(!mysqli_stmt_prepare($stmt3, $sql3)){
                echo "SQL error2";
            }else{
                mysqli_stmt_bind_param($stmt3, "iisss", $right_answer, $wrong_answer, $uuid, $category, $tested_value);
                mysqli_stmt_execute($stmt3);
            }
        }
    }
}

if($_POST["method"] == "saveDrawing"){
    $img = $_POST['imgBase64'];
    $category = $_POST['category'];
    $date = $_POST['date'];
    $uniqid = uniqid();
    $img = str_replace('data:image/png;base64,', '', $img);
    $img = str_replace(' ', '+', $img);
    $fileData = base64_decode($img);
    $fileName = 'savedImages/'.$category.'/'.$uniqid."_".$date.'.png';
    file_put_contents($fileName, $fileData);
}

?>