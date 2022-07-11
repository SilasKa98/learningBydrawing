<?php

if(isset($_POST["login_submit"])){
	include '../db_connector.php';
	
	$mailuid = $_POST["mailuid"];
	$pwd = $_POST["pwd"];
	
	if(empty($mailuid) || empty($pwd)){
		header("Location: ../index.html?error=emptyfields");
		exit();
	}else{
		$sql = "select * from loginsystem where user_id=? OR user_email=?;";
		$stmt = mysqli_stmt_init($connection);
		if(!mysqli_stmt_prepare($stmt,$sql)){
			header("Location: ../index.html?error=sqlerror");
			exit();
		}else{
			mysqli_stmt_bind_param($stmt,"ss", $mailuid, $mailuid );
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			if($row = mysqli_fetch_assoc($result)){
				$pwdCheck = password_verify($pwd, $row["user_pwd"]);
				if($pwdCheck == false){
					header("Location: ../index.html?error=wrongpwd");
					exit();
				}
				elseif($pwdCheck == true){
					session_start();
					$_SESSION["algID"] = $row["ID"];
					$_SESSION["idUser"] = $row["user_id"];
					
					header("Location: ../settings.php?login=success");
					exit();
				}
				else{
					header("Location: ../index.html?error=wrongpwd");
					exit();
				}
			}else{
				header("Location: ../index.html?error=nouser");
				exit();
			}
		}
	}
	
	
}else{
	header("Location: ../index.html");
	exit();
}