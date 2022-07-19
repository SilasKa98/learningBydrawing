<?php
session_start();
if(isset($_SESSION["idUser"])){
    include 'db_connector.php';
    $allCategorys = [];
    $sql = "select category as cat from datasets;";
    $stmt = mysqli_stmt_init($connection);
    if(!mysqli_stmt_prepare($stmt, $sql)){
    echo "SQL Statement failed";
    }else{
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = $result->fetch_assoc()) {
            array_push($allCategorys,$row["cat"]);
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="styles/background.css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="externScripts/tf.min.js"></script>
    <script>
        function highliteCategory(e){
            console.log(e);
            console.log(e.childNodes[0]);
            let clickedBox = e;
            let clickedCategory = e.childNodes[0].innerHTML;
            let allBtns = document.querySelectorAll(".category");
            for(let i=0;i<allBtns.length;i++){
                if(allBtns[i].style.backgroundColor == "green"){         
                    let greenBtn = allBtns[i];
                    greenBtn.style.backgroundImage = "linear-gradient( 135deg, #43CBFF 10%, #9708CC 100%)";
                } 
            }
            e.style.backgroundImage = "none";
            e.style.backgroundColor = "green";

            document.getElementById("choosenCategory").value = clickedCategory;
        }
        function submitForm(){
            var counter = 0;
            let allBtns = document.querySelectorAll(".category");
            for(let i=0;i<allBtns.length;i++){
                if(allBtns[i].style.backgroundColor == "green"){         
                    counter++;
                } 
            }
            if(counter > 0){
                document.getElementById("startForm").submit();
            }else{
                //TODO placeholder alert, needs to be replaced by nice looking error
                alert("Bitte eine Kategorie auswählen");
            }
        }
        function backgroundRandomizer(){
            setInterval(function () {
                var rndChars = "abcdefghijklmnopqrstuwvxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

                let allRndChars = document.querySelectorAll(".rndChar");
                console.log(allRndChars);
                for(let i=0;i<allRndChars.length;i++){
                    let rndChar= rndChars[Math.floor(Math.random() * rndChars.length)];
                    allRndChars[i].innerHTML = rndChar
                }
            }, 22000);
        }
    </script>
</head>
<body class="area" onload="backgroundRandomizer()">
    <form action="login_system/logout_script.php" method='post' id='logoutForm'>
        <input type="submit" id="logout" value="Logout"></button>
        <p id='loginUserHello'>Hallo <?=$_SESSION["idUser"] ?></p>
    </form>
    <h1 id="headline">Einstellungen</h1>
    <div class="content">
        <div id="categoryWrapper">
            <!--Later fetch the categorys from the database here: corresponding table is "datasets"-->
            <?php
                for($i=0;$i<count($allCategorys);$i++){
                    print '<div class="category" onclick="highliteCategory(this)">';
                    print'<p>'.$allCategorys[$i].'</p>';
                    print '</div>';
                }
            ?>
        </div>
    </div>
    <div class="content" id="content2">
        <form action="drawing.php" method="post" id="startForm">
                <label>Anzahl der Übungen: 
                    <input type="number" name="repeats" min="1" value="1" style="width: 6%;">
                </label><br><br>
                Intelligentes Lernen: 
                <label class="switch">
                    <input type="checkbox" name="intLearning">
                    <span class="slider round"></span>
                </label><br><br>
                <input type="hidden" id="choosenCategory" name="category">
        </form>
    </div>
    <button id="startBtn" onclick="submitForm()">Übung Starten</button>

    <!--for the Background animation-->
    <ul class="circles">
        <li></li>
        <li></li>
        <li class="rndChar">a</li>
        <li class="rndChar">4</li>
        <li class="rndChar">F</li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li class="rndChar">K</li>
        <li class="rndChar">8</li>
        <li></li>
        <li></li>
        <li></li>
        <li class="rndChar">u</li>
        <li class="rndChar">L</li>
    </ul>
</body>

</html>

<?php
}else{
    echo "Access denied";
}
?>


