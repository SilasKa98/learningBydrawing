<?php
session_start();
if(isset($_SESSION["idUser"])){
    include 'db_connector.php';
    $uuid = $_SESSION["uuid"];

    $allCategorys = [];
    $allLearningplans = [];
    $allLearningplanIds = [];
    $sql = "select category as cat from datasets;";
    $stmt = mysqli_stmt_init($connection);
    if(!mysqli_stmt_prepare($stmt, $sql)){
    echo "SQL Statement failed";
    }else{
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = $result->fetch_assoc()) {
            array_push($allCategorys,$row["cat"]);
            $allLearningplans[$row["cat"]] = [];
            $allLearningplanIds[$row["cat"]] = [];
        }
    }

    //get all Learning plans and save them into array
    $sql = "select * from learningplans where uuid=?;";
    $stmt = mysqli_stmt_init($connection);
    if(!mysqli_stmt_prepare($stmt, $sql)){
    echo "SQL Statement failed";
    }else{
        mysqli_stmt_bind_param($stmt, "s", $uuid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = $result->fetch_assoc()) {
            array_push($allLearningplans[$row["category"]],$row["name"]);
            array_push($allLearningplanIds[$row["category"]],$row["ID"]);
        }
    }

    //check if user allows to save his images 
    $sql = "select allowImageSave from loginsystem where uuid=?;";
    $stmt = mysqli_stmt_init($connection);
    if(!mysqli_stmt_prepare($stmt, $sql)){
    echo "SQL Statement failed";
    }else{
        mysqli_stmt_bind_param($stmt, "s", $uuid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = $result->fetch_assoc()) {
        $allowImageSave = $row["allowImageSave"];
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

            //clear the currentLearn plans
            document.getElementById("lpSelectionField").innerHTML = "";

            //create option elements for each learnplan
            let allLearnPlans = document.querySelectorAll("."+clickedCategory);
            for(let i=0;i<allLearnPlans.length;i++){
                let opt = document.createElement('option');
                opt.value = allLearnPlans[i].id;
                opt.innerHTML = allLearnPlans[i].value;
                console.log(opt);
                document.getElementById("lpSelectionField").appendChild(opt);
            }
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
                alert("Bitte eine Kategorie ausw??hlen");
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

        function toggleLearningPlans(){
            let greenCount = 0;
            let allBtns = document.querySelectorAll(".category");
            for(let i=0;i<allBtns.length;i++){
                if(allBtns[i].style.backgroundColor == "green"){
                    greenCount++;       
                } 
            }
            if(greenCount == 0){
                //maybe replace with nice error message
                alert("Du musst zuerst oben eine der Kategorien ausw??hlen!");
                $("#learningPlanSwitch").prop("checked", false);
                return;
            }
            $("#lpSelectionWrapper").fadeToggle();
            $("#repeatLabelWrap").fadeToggle();
        }


        function hideLearningPlans(){
            $("#learningplanWrapper").fadeToggle();
            $("#repeatLabelWrap").show();
            $("#learningPlanSwitch").prop("checked", false);
            $("#lpSelectionWrapper").hide();
            
        }

        function allowSavingImages(e){
            console.log(e.checked);
            let checkboxStatus;
            if(e.checked == true){
                checkboxStatus = 1;
            }else{
                checkboxStatus = 0;
            }
            $.ajax({
                type: "POST",
                url: "backend.php",
                data: {
                    checkboxStatus: checkboxStatus,
                    method: "checkImageSavePermission",
                },
                success: function(result, message, response) {
                    console.log(result);
                    console.log(message);
                    console.log(response);
                }
            });
        }
    </script>
</head>
<body class="area" onload="backgroundRandomizer()">
    <?php
        //loop through the learning plans and print them in hidden input to be able to access in the frontend
        for($i=0;$i<count($allCategorys);$i++){
            for($a=0;$a<count($allLearningplans[$allCategorys[$i]]);$a++){
                print "<input type='hidden' style='display:none;' id='".$allLearningplanIds[$allCategorys[$i]][$a]."' class='".$allCategorys[$i]."' value='".$allLearningplans[$allCategorys[$i]][$a]."'>";
            }
        }   
    ?>
    <form action="login_system/logout_script.php" method='post' id='logoutForm'>
        <div id="userWrapper" onclick="location.href='resultsAndPlans.php'">
            <img src="media/circle-user.svg" id="userCircle">
            <p id='loginUserHello'><?=$_SESSION["idUser"] ?></p>
        </div>
        <input type="submit" id="logout" value="Logout"></button>
    </form>
    <div id='allowSavingWrapper'>Meine Bilder speichern
        <div class="tooltip">&#x1F6C8;
            <span class="tooltiptext">Durch das aktivieren dieser Funktion stimmst du zu, dass deine gezeichneten ??bungen gespeichert und verarbeitet werden d??rfen. 
                Dies tr??gt zur Verbesserung der App bei.
            </span>
        </div>
        <label class="switch" id="savingImagesSwitch">
            <input type="checkbox" id="allowSaving" onclick="allowSavingImages(this)" <?php if($allowImageSave == 1){print "checked";} ?>>
            <span class="slider round" id="savingImagesSlider"></span>
        </label>
    </div>
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
                <div id='repeatsWrapper'>
                    <label id='repeatLabelWrap'>Anzahl der ??bungen: 
                        <input type="number" id="repeats" name="repeats" min="1" value="1" style="width: 6%;">
                    </label>
                </div>
                <div id='intLearningWrapper'>
                    Intelligentes Lernen: 
                    <label class="switch">
                        <input type="checkbox" name="intLearning" id='intLearningSwitch' onclick="hideLearningPlans()">
                        <span class="slider round"></span>
                    </label>
                </div>
                <div id='learningplanWrapper'>
                    <div id="useLearnplanWrapper">
                        Lernplan verwenden: 
                        <label class="switch">
                            <input type="checkbox" name="enableLearningplan" id='learningPlanSwitch' onclick="toggleLearningPlans()">
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <div id="lpSelectionWrapper">
                        <label id="learningPlanSelection">
                            Lernplan ausw??hlen: 
                            <select id='lpSelectionField' name='selectedLearningplan'>
                            </select>
                        </label>
                    </div>
                </div>
                <input type="hidden" id="choosenCategory" name="category">
        </form>
    </div>
    <button id="startBtn" onclick="submitForm()">??bung Starten</button>

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


