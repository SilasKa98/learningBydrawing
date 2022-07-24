<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="../styles/loginStyle.css">
		<link rel="stylesheet" href="../styles/background.css">
	</head>
	<body class="area">
	<a href="../index.html" id="back_button">zurück</a>
	<h1 id="headline">Account erstellen</h1>
		<div id="signupWrapper">
			<?php
			  error_reporting(E_ERROR | E_PARSE);
				if(isset($_GET["error"])){
					if($_GET["error"] == "emptyfields"){
						echo "<p>Bitte füllen Sie alle Felder aus!</p>";
					}
					elseif($_GET["error"] == "invalidmailuser_id"){
						echo "<p>Ungültige E-mail oder ungültiger Benutzername!</p>";
					}
					elseif($_GET["error"] == "invalidmail"){
						echo "<p>Ungültige E-mail!</p>";
					}
					elseif($_GET["error"] == "invaliduser_id"){
						echo "<p>Ungültiger Benutzername!</p>";
					}
					elseif($_GET["error"] == "passwordcheck"){
						echo "<p>Die zwei Passwörter stimmen nicht überein!</p>";
					}
					elseif($_GET["error"] == "usertakenoremailtaken"){
						echo "<p>Der Gewünschte Benutzername oder die gewünschte E-mail sind bereits vergeben!</p>";
					}
					
				}
				elseif($_GET["signup"] == "success"){
					echo "<p>Der Account wurde Erfolgreich angelegt!</p>";
					header("Location:../index.html?registration=success");
				}

			?>
			<form action="signup_script.php" method="post">
				<input type="text" name="user_id" placeholder="Benutzername..."><br><br>
				<input type="text" name="mail" placeholder="E-mail..."><br><br>
				<input type="password" name="pwd" placeholder="Passwort..."><br><br>
				<input type="password" name="pwd_repeat" placeholder="Passewort wiederholen..."><br><br>
				<input type="submit" id="createUserSubmit" name="signup_submit" value="Registrieren"><br><br>
			</form>
		</div>
	</body>
</html>