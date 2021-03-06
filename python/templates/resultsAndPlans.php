<?php
include 'db_connector.php';
session_start();
$uuid = $_SESSION["uuid"];

$categorys = [];
$datasets =[];
$sql = "select * from datasets;";
$stmt = mysqli_stmt_init($connection);
if(!mysqli_stmt_prepare($stmt, $sql)){
echo "SQL Statement failed";
}else{
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = $result->fetch_assoc()) {
        array_push($categorys,$row["category"]);
        array_push($datasets,$row["data"]);
    }
}

$testedCategoryValues=[];
$valueRights = [];
$valueWrongs = [];
for($i=0;$i<count($categorys);$i++){
    array_push($testedCategoryValues,[]);
    array_push($valueRights,[]);
    array_push($valueWrongs,[]);
    $sql = "select * from learningresults where uuid=? and category=? order by category desc, wrong_answers desc;";
    $stmt = mysqli_stmt_init($connection);
    if(!mysqli_stmt_prepare($stmt, $sql)){
    echo "SQL Statement failed";
    }else{
        mysqli_stmt_bind_param($stmt, "ss", $uuid, $categorys[$i]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = $result->fetch_assoc()) {
            array_push($testedCategoryValues[$i],$row["tested_value"]);
            array_push($valueRights[$i],$row["right_answers"]);
            array_push($valueWrongs[$i],$row["wrong_answers"]);
        }
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
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="styles/resultAndPlans.css">
    <link rel="stylesheet" href="styles/background.css">
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script>
        $( function() {
            $( "#accordion" ).accordion(
                { header: "h3",
                  collapsible: true,
                  active: false,
                  heightStyle: "content" 
                }
            );
        });

        function fillBars(){
            var allRightBars = document.querySelectorAll(".rightBar");
            var allWrongBars = document.querySelectorAll(".wrongBar");
            for(let i=0;i<allRightBars.length;i++){
                allRightBars[i].style.backgroundPosition = "right bottom";
            }
            for(let i=0;i<allWrongBars.length;i++){
                allWrongBars[i].style.backgroundPosition = "right bottom";
            }
            setTimeout(function() {
                for(let i=0;i<allRightBars.length;i++){
                    allRightBars[i].style.backgroundPosition = "left bottom";
                }
                for(let i=0;i<allWrongBars.length;i++){
                    allWrongBars[i].style.backgroundPosition = "left bottom";
                }
            }, 200);
        }

        $( function() {
            $( "#tabs").tabs({
                show: { effect: "fade", duration: 700, }
            });
        } );

        function showCreateArea(e){
            if(e.nextElementSibling.style.display == "block"){
                e.nextElementSibling.style.display = "none";
            }else{
                e.nextElementSibling.style.display = "block";
            } 
        }

        function createNewPlan(e){
            let clickedElement = e.innerHTML;
            let targetArea = e.parentNode.nextElementSibling;

            targetArea.innerHTML += "<span class='innerPlanElem'>"+clickedElement+"</span>";
        }

        function getCategory(){
            let allTabs = document.querySelectorAll(".ui-tabs-tab");
            for(let i=0;i<allTabs.length;i++){    
                if(allTabs[i].classList.contains("ui-tabs-active")){
                    var category = allTabs[i].childNodes[0].innerHTML;
                }
            }
            return category;
        }

        function savePlan(e){
            let allValues = [];
            let allChilds = e.previousElementSibling.childNodes;
            for(let i=0;i<allChilds.length;i++){
                if(allChilds[i].nodeName == "SPAN"){
                    allValues.push(allChilds[i].innerHTML);
                }
            }

            let category = getCategory();
            let name = e.previousElementSibling.previousElementSibling.previousElementSibling.value;
            
            $.ajax({
                type: "POST",
                url: "backend.php",
                data: {
                    category: category,
                    name: name,
                    method: "saveLearningPlan",
                    allValues: allValues
                },
                success: function(result, message, response) {
                    location.reload();
                }
            });
        }

        function deletePlan(id){
            $.ajax({
                type: "POST",
                url: "backend.php",
                data: {
                    delid: id,
                    method: "deletePlan"
                },
                success: function(result, message, response) {
                    location.reload();
                }
            });
        }
    </script>
    <style>
    </style>
</head>
<body class="area">
    <a href="settings.php" id="back_button">zur??ck</a>
   <h1 id="headline">Ergebnisse und Lernpl??ne</h1>
    <div id="accordion">
    <h2 class="secHeadline">??berpr??fe deine bisherigen Lernergebnisse.</h2>
        <?php
         for($i=0;$i<count($categorys);$i++){
            print "<h3 class='accHeader' onclick='fillBars()'>".$categorys[$i]."</h3>";
            print "<div>";
                if(count($testedCategoryValues[$i]) == 0){
                    print "F??r diese Kategorie wurden noch keine Lernerfolge aufgezeichnet!";
                }else{
                    for($a=0;$a<count($testedCategoryValues[$i]);$a++){
                        $totalSum = $valueRights[$i][$a]+$valueWrongs[$i][$a];
                        $rightRatio = ($valueRights[$i][$a]/$totalSum)*100;
                        $wrongRatio = ($valueWrongs[$i][$a]/$totalSum)*100;
           
                        $wrongRatioStyle = (round($wrongRatio)-10);
                        $rightRatioStyle = (round($rightRatio)-10);
                        $showWrong = "inline-block";
                        $showRight = "inline-block";
                        $wrongDelay = "1.4s";
                        $borderRadiusWrong = "";
                        $borderRadiusRight = "";
                        if($rightRatio == 0){
                            $showRight = "none";
                            $borderRadiusWrong = "7px";
                            $wrongDelay = "0s";
                        }
                        if($wrongRatio == 0){
                            $showWrong = "none";
                            $borderRadiusRight = "7px";
                        }
                        print "<div class='resultWrapper'><p class='testedValue'>Getesteter Wert (Anzahl/Prozent): <b>".$testedCategoryValues[$i][$a]."</b></p><p class='rightBar' style='width:".$rightRatioStyle."%;display:".$showRight.";border-radius:".$borderRadiusRight.";'>".$valueRights[$i][$a]." Richtig (".round($rightRatio,0)."%) </p><p class='wrongBar' style='width:".$wrongRatioStyle."%;display:".$showWrong.";border-radius:".$borderRadiusWrong.";transition-delay:".$wrongDelay.";'>".$valueWrongs[$i][$a]." Falsch (".round($wrongRatio,0)."%) </p></div>";
                    }
                }
            print "</div>";
        }
        ?>
    </div>
    <div id="learnCardWrapper">
        <h2 class="secHeadline">Erstelle oder verwalte deine Lernpl??ne.</h2>
        <div id="tabs">
            <ul>
                <?php
                for($i=0;$i<count($categorys);$i++){
                    $tab = $i+1;
                    print "<li><a href='#tabs-".$tab."'>".$categorys[$i]."</a></li>";
                }
                ?>
            </ul>
            <?php
            for($i=0;$i<count($categorys);$i++){
                $expDataset = explode(",",$datasets[$i]);
                $tab = $i+1;
                print "<div id='tabs-".$tab."'>";
                    print"<button class='createNewBtn' onclick='showCreateArea(this)'>+</button>";
                    print "<div class='createNewPlan'>";
                        print "Lernplan Name: <input type='text' id='catName'>";
                        print"<div class='datasets'>M??gliche Werte: ";
                        for($a=0;$a<count($expDataset);$a++){
                        print "<button class='dataCards' onclick='createNewPlan(this)'>".$expDataset[$a]."</button>"; 
                        }  
                        print"</div>";
                        print "<div class='createdPlan'>";
                            print "Learnplan: ";
                        print "</div>";
                        print "<button onclick='savePlan(this)' class='saveBtn'>Lernplan speichern</button>";
                    print"</div>";
                    print"<div class='existingPlans'>";
                
                    $sql = "select * from learningplans where uuid=? and category=?;";
                    $stmt = mysqli_stmt_init($connection);
                    if(!mysqli_stmt_prepare($stmt, $sql)){
                    print "SQL Statement failed";
                    }else{
                        mysqli_stmt_bind_param($stmt, "ss", $uuid, $categorys[$i]);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        if ($result->num_rows == 0) {
                            print "<p>F??r diese Kategorie wurden noch keine Lernpl??ne erstellt.</p>";
                        }else{
                            while ($row = $result->fetch_assoc()) { 
                                $expPlanData = explode(",",$row["data"]);
                                print "<p>Learnplan<b> ".$row["name"].":</b> ";
                                for($z=0;$z<count($expPlanData);$z++){
                                    print "<span class='learnplanCards'>".$expPlanData[$z]."</span>";
                                }
                                print "<span class='delBtn' onclick='deletePlan(".$row["ID"].")'>x</span>";
                                print "</p>";
                            }
                        }
                    }
                    print"</div>";
                print"</div>";
            }
            ?>
        </div>
    </div>



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