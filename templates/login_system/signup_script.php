<?php



if(isset($_POST["signup_submit"])){
	
	include '../db_connector.php';

	$username = $_POST["user_id"];
	$mail = $_POST["mail"];	
	$pwd = $_POST["pwd"];		
	$pwd_repeat = $_POST["pwd_repeat"];	

	
	if(empty($username)|| empty($mail) || empty($pwd) || empty($pwd_repeat)){
		header("Location: signup.php?error=emptyfields&user_id=".$username."&mail=".$mail);
		exit();
	}
	
	else if(!filter_var($mail, FILTER_VALIDATE_EMAIL) && !preg_match("/^[a-zA-Z0-9]*$/", $username)){
		header("Location: signup.php?error=invalidmailuser_id");
		exit();
	}
	
	else if(!filter_var($mail, FILTER_VALIDATE_EMAIL)){
		header("Location: signup.php?error=invalidmail&user_id=".$username);
		exit();
	}
	else if(!preg_match("/^[a-zA-Z0-9]*$/", $username)){
		header("Location: signup.php?error=invaliduser_id&mail=".$mail);
		exit();
	}
	else if($pwd !== $pwd_repeat){
		header("Location: signup.php?error=passwordcheck&user_id=".$username."&mail=".$mail);
		exit();
	}
	
	
	else{
		//check if user is already taken
		$sql = "Select user_id,user_email from loginsystem WHERE user_id=? or user_email=?;";
		$stmt = mysqli_stmt_init($connection);
		if(!mysqli_stmt_prepare($stmt, $sql)){
			header("Location: signup.php?error=sqlerror");
			exit();
		}else{
			mysqli_stmt_bind_param($stmt, "ss", $username, $mail);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_store_result($stmt);
			$resultCheck = mysqli_stmt_num_rows($stmt);
			if($resultCheck > 0){
				header("Location: signup.php?error=usertakenoremailtaken");
				exit();
			}
					

		
			
			//insert if no errorchecks get triggered
			else{
				$sql = "INSERT into loginsystem (user_id, user_email, user_pwd) values (?,?,?);";
				$stmt = mysqli_stmt_init($connection);
				if(!mysqli_stmt_prepare($stmt, $sql)){
					header("Location: signup.php?error=sqlerror");
					exit();
				}else{
					$hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
					
					mysqli_stmt_bind_param($stmt, "sss", $username,$mail,$hashedPwd);
					mysqli_stmt_execute($stmt);
					header("Location: signup.php?signup=success");
					exit();
				}
			}
		}
	}
	mysqli_stmt_close($stmt);
	mysqli_close($connection);
}
else{
	header("Location: signup.php");
	exit();
}
