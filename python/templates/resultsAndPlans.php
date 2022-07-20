<?php
include 'db_connector.php';
session_start();
$uuid = $_SESSION["uuid"];

$categorys = [];
$sql = "select category from datasets;";
$stmt = mysqli_stmt_init($connection);
if(!mysqli_stmt_prepare($stmt, $sql)){
echo "SQL Statement failed";
}else{
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = $result->fetch_assoc()) {
       array_push($categorys,$row["category"]);
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
        } );
    </script>
    <style>
    </style>
</head>
<body class="area">
    <h1>Ihre Ergebnisse</h1>
    <div id="accordion">
        <?php
         for($i=0;$i<count($categorys);$i++){
            print "<h3 class='accHeader'>".$categorys[$i]."</h3>";
            print "<div>";
                if(count($testedCategoryValues[$i]) == 0){
                    print "Für diese Kategorie wurden noch keine Lernerfolge aufgezeichnet!";
                }else{

                        for($a=0;$a<count($testedCategoryValues[$i]);$a++){
                            $totalSum = $valueRights[$i][$a]+$valueWrongs[$i][$a];
                            $rightRatio = ($valueRights[$i][$a]/$totalSum)*100;
                            $wrongRatio = ($valueWrongs[$i][$a]/$totalSum)*100;

                            
                            $wrongRatioStyle = (round($wrongRatio)-10);
                            $rightRatioStyle = (round($rightRatio)-10);
                            print "<div class='resultWrapper'><p class='testedValue'>Getesteter Wert (Anzahl/Prozent): <b>".$testedCategoryValues[$i][$a]."</b></p><p class='rightBar' style='width:".$rightRatioStyle."%;background-color:green;'>".$valueRights[$i][$a]." Richtig (".round($rightRatio,0)."%) </p><p class='wrongBar' style='width:".$wrongRatioStyle."%;background-color:red;'>".$valueWrongs[$i][$a]." Falsch (".round($wrongRatio,0)."%) </p></div>";
                        }
                }
            print "</div>";
        }
        ?>
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