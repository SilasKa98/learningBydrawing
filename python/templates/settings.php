<?php
session_start();
if(isset($_SESSION["idUser"])){
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="background.css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="tf.min.js"></script>
    <script>
        function highliteCategory(e){
            let clickedBox = e;
            let clickedCategory = e.childNodes[1].innerHTML;
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
            <div class="category" onclick="highliteCategory(this)">
                <p>Formen</p>
            </div>
            <div class="category" onclick="highliteCategory(this)">
                <p>Buchstaben</p>
            </div>
            <div class="category" onclick="highliteCategory(this)">
                <p>Zahlen</p>
            </div>
            <div class="category" onclick="highliteCategory(this)">
                <p>Japanisch</p>
            </div>
        </div>
    </div>
    <div class="content" id="content2">
        <form action="drawing.php" method="post" id="startForm">
                <label>Anzahl der Übungen: <input type="number" name="repeats" min="1" value="1"></label><br><br>
                <label>Nur zuvor falsch gemachtes Üben <input type="checkbox" name="falseTraining"></label><br><br>
                <input type="hidden" id="choosenCategory" name="category">
        </form>
    </div>
    <button id="startBtn" onclick="submitForm()">Übung Starten</button>

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
}
?>


