<?php
include 'db_connector.php';
session_start();
if(isset($_SESSION["idUser"])){
    //check the parameters intlearning and learnplans if its on or off, later needed on a few points to change the used data
    if(isset($_POST["intLearning"])){
        $inteligentLearning = "on";
    }else{
        $inteligentLearning = "off";
    }

    if(isset($_POST["enableLearningplan"])){
        $enableLearningplan = "on";
    }else{
        $enableLearningplan = "off";
    }

    //when learningplan not selected just load all values possible
    if($enableLearningplan == "off"){
        //get dataset of the choosen category
        $sql = "select * from datasets where category=?;";
        $stmt = mysqli_stmt_init($connection);
        if(!mysqli_stmt_prepare($stmt, $sql)){
        echo "SQL Statement failed";
        }else{
            mysqli_stmt_bind_param($stmt, "s",$_POST["category"]);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = $result->fetch_assoc()) {
                $dataset = $row["data"];
            }
        }
    }

    //when learningplan selected only load learning plan values
    if($enableLearningplan == "on"){
        //select learning plan from database
        $sql = "select * from learningplans where ID=? and uuid=?;";
        $stmt = mysqli_stmt_init($connection);
        if(!mysqli_stmt_prepare($stmt, $sql)){
        echo "SQL Statement failed";
        }else{
            mysqli_stmt_bind_param($stmt, "is",$_POST["selectedLearningplan"],$_SESSION["uuid"]);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = $result->fetch_assoc()) {
                $dataset = $row["data"];
                $selLearnplan = $row["name"];
            }
        }
    }

    //when int learning is on choos values inteligent - frontend only allows this when learningplan is off, so here the learning plan off dataset is used all the time
    $convWeakValues=null;
    if($inteligentLearning == "on"){
        $weakValues = [];
        $sql = "select right_answers,wrong_answers,tested_value from learningresults where uuid=? and category=?;";
        $stmt = mysqli_stmt_init($connection);
        if(!mysqli_stmt_prepare($stmt, $sql)){
        echo "SQL Statement failed";
        }else{
            mysqli_stmt_bind_param($stmt, "ss", $_SESSION["uuid"], $_POST["category"]);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = $result->fetch_assoc()) {
                if($row["wrong_answers"] > 0 && (($row["right_answers"] + $row["wrong_answers"]) / $row["wrong_answers"])<= 2){
                    array_push($weakValues,$row["tested_value"]);
                }
            }
            //Random take numbers from the dataset if the total number of values for intelligent learning is too less
            $datasetArr = explode(",",$dataset);
            if(count($weakValues) < $_POST["repeats"]){
                $diff = $_POST["repeats"] - count($weakValues);
                while($diff > 0){
                    $diff--;
                    $rndChoosenKey = array_rand($datasetArr, 1);
                    array_push($weakValues,$datasetArr[$rndChoosenKey]);
                }
            }
            $convWeakValues = implode(",",$weakValues);
        }
    }

    //adjust the repeats--> if a learningplan is selected in the settings just take the length of the this plan ($_POST["repeats"] is always 1 if learnplan is choosen)
    //however $_POST["repeats"] wont be used because of the following logic when learnplan is active
    if($enableLearningplan == "on"){
        $repeats = count(explode(",",$dataset));
    }else{
        $repeats = $_POST["repeats"];
    }
    
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zeichnen</title>
    <link rel="stylesheet" href="styles/drawingStyle.css">
    <link rel="stylesheet" href="styles/background.css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <!--<script src="externScripts/tf.min.js"></script>-->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@2.0.0/dist/tf.min.js"></script>
    <script>
        function backgroundRandomizer(){
            setInterval(function () {
                var rndChars = "きすつなはまやれをきすつなはまやれをabcdefghijklmnopqrstuwvxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890123456789";
                let allRndChars = document.querySelectorAll(".rndChar");
                for(let i=0;i<allRndChars.length;i++){
                    let rndChar= rndChars[Math.floor(Math.random() * rndChars.length)];
                    allRndChars[i].innerHTML = rndChar
                }
            }, 22000);
        }
    </script>
</head>
<body class="area" onload="categoryChooser();backgroundRandomizer();loadVoices()">
    <a href="settings.php" id="back_button">zurück</a>
    <h1 id="headline"><?= $_POST["category"]?> lernen</h1>
    <div id="contentWrapper">
        <div id="wrapper1">
            <div id="infoDisplay">
                <p>Kategorie: <span class="infoCurrent"><?= $_POST["category"]?></span></p>
                <p>Anzahl der Übungen: <span class="infoCurrent"><?= $repeats?></span></p>
                <p>Intelligentes lernen: <span class="infoCurrent"><?= $inteligentLearning?></span></p>
                <?php if($enableLearningplan == "on"){ ?> <p>Lernplan: <span class="infoCurrent"><?= $selLearnplan?></span></p> <?php } ?>
                <p id="displayCurrentRepeat">Durchlauf:<span class="infoCurrent"><span id="repeatsDisp"></span></span></p>
            </div>
            <input type="hidden" id="selectedCategory" value="<?= $_POST["category"]?>">
            <input type="hidden" id="selectedRepeats" value="<?= $repeats?>">
            <input type="hidden" id="intLearning" value="<?= $inteligentLearning ?>">
            <input type="hidden" id="hasLearningplan" value="<?= $enableLearningplan ?>">
            <div id="taskWrapper">
                <?php
                    if($_POST["category"] == "Zahlen"){
                        print "<p>Zeichnen Sie eine: ";
                    }else{
                        print "<p>Zeichnen Sie ein: ";
                    }
                ?>        
                <img src="media/speaker.svg" width="20px" height="20px" id="playAudio" onclick="textToSpeech()"></p> 
                <div id="task"></div>
            </div>
        </div>
        <div id="canvasWrapper">
            <div id="btnWrapper">
                <button id="next" onclick="categoryChooser()" style="display:none;">Nächste Übung</button>
                <button id="doPredict">Zeichnung Überprüfen</button>
                <button id="resetBtn">Zeichenfeld leeren</button>
            </div>
        </div>
        <div id="pieChartWrapper">
            <h2>Übung Abgeschlossen</h2>
            <canvas id="pieChartCanvas" style="width:400px;max-width:400px;display:none;"></canvas>
            <div id="pieChartBtnWrapper">
                <a href="settings.php" id="endTraining" style="display:none;">Übung Beenden</a>
                <a href="#" id="redoTraining" style="display:none;" onclick="location.reload()">Übung Wiederholen</a>
            </div>
        </div>
    </div>
    <div id="result"></div>


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
<script>

    var currentRun = 1;
    var selectedCategory = document.getElementById("selectedCategory").value;
    var taskField = document.getElementById("task");
    var resultField = document.getElementById("result");

    var canvasWidth = 600;
    var canvasHeight = 600;
    var canvasStrokeStyle = "white";
    var canvasLineJoin = "round";
    var canvasLineWidth = 30;
    var canvasBackgroundColor = "black";
    var canvasId = "canvas";

    var clickX = new Array();
    var clickY = new Array();
    var clickD = new Array();
    var drawing;


    // Create the canvas
    var canvasBox = document.getElementById('canvasWrapper');
    var canvas = document.createElement("canvas");

    canvas.setAttribute("width", canvasWidth);
    canvas.setAttribute("height", canvasHeight);
    canvas.setAttribute("id", canvasId);
    canvas.style.backgroundColor = canvasBackgroundColor;
    canvasBox.prepend(canvas);
    if(typeof G_vmlCanvasManager != 'undefined') {
    canvas = G_vmlCanvasManager.initElement(canvas);
    }

    context = canvas.getContext("2d");


    //---------------------------------------------------------//
    // Following are all functions to track the canvas drawing //
    //---------------------------------------------------------//

    // track mouse down event
    $("#canvas").mousedown(function(e) {
        var rect = canvas.getBoundingClientRect();
        var mouseX = e.clientX- rect.left;;
        var mouseY = e.clientY- rect.top;
        drawing = true;
        addUserGesture(mouseX, mouseY);
        drawOnCanvas();
    });


    // track mouse move event
    $("#canvas").mousemove(function(e) {
        if(drawing) {
            var rect = canvas.getBoundingClientRect();
            var mouseX = e.clientX- rect.left;;
            var mouseY = e.clientY- rect.top;
            addUserGesture(mouseX, mouseY, true);
            drawOnCanvas();
        }
    });


    // track mouse up event
    $("#canvas").mouseup(function(e) {
        drawing = false;
    });


    // track mouse leave event
    $("#canvas").mouseleave(function(e) {
        drawing = false;
    });


    // adding the start of drawing by click
    function addUserGesture(x, y, dragging) {
        let task = document.getElementById("task");
        if(task.innerHTML != ""){
            clickX.push(x);
            clickY.push(y);
            clickD.push(dragging);
        }
    }



    //--------------------------//
    //implementing touch support//
    //--------------------------//

    //track touch start
    canvas.addEventListener("touchstart", function (e) {
        if (e.target == canvas) {
            e.preventDefault();
        }

        var rect = canvas.getBoundingClientRect();
        var touch = e.touches[0];

        var mouseX = touch.clientX - rect.left;
        var mouseY = touch.clientY - rect.top;

        drawing = true;
        addUserGesture(mouseX, mouseY);
        drawOnCanvas();

    }, false);


    //track touch move
    canvas.addEventListener("touchmove", function (e) {
        if (e.target == canvas) {
            e.preventDefault();
        }
        if(drawing) {
            var rect = canvas.getBoundingClientRect();
            var touch = e.touches[0];

            var mouseX = touch.clientX - rect.left;
            var mouseY = touch.clientY - rect.top;

            addUserGesture(mouseX, mouseY, true);
            drawOnCanvas();
        }
    }, false);


    //track touch end
    canvas.addEventListener("touchend", function (e) {
        if (e.target == canvas) {
            e.preventDefault();
        }
        drawing = false;
    }, false);


    //track touch leave
    canvas.addEventListener("touchleave", function (e) {
        if (e.target == canvas) {
            e.preventDefault();
        }
        drawing = false;
    }, false);


    // implementing the drawing in the canvas
    function drawOnCanvas() {
        context.clearRect(0, 0, context.canvas.width, context.canvas.height);

        context.strokeStyle = canvasStrokeStyle;
        context.lineJoin    = canvasLineJoin;
        context.lineWidth   = canvasLineWidth;

        for (var i = 0; i < clickX.length; i++) {
            context.beginPath();
            if(clickD[i] && i) {
                context.moveTo(clickX[i-1], clickY[i-1]);
            } else {
                context.moveTo(clickX[i]-1, clickY[i]);
            }
            context.lineTo(clickX[i], clickY[i]);
            context.closePath();
            context.stroke();
        }
    }


    // clear the canvas 
    $("#next, #resetBtn").click(async function () {
        context.clearRect(0, 0, canvasWidth, canvasHeight);
        clickX = new Array();
        clickY = new Array();
        clickD = new Array();
    });



    //---------------------------------------------------------//
    //               End of Canvas Drawing                     //
    //---------------------------------------------------------//



    var totalRight = 0;
    var totalWrong = 0;
    var totalUnknown = 0;
    var threshold = 0.7;

    function categoryChooser(){
        let selectedRepeats = parseInt(document.getElementById("selectedRepeats").value);
        let checkIntLearning = document.getElementById("intLearning").value;
        let checkLearningplan = document.getElementById("hasLearningplan").value;

        //hide the next button and show the predictBtn and resetBtn again till the next predict is done
        document.getElementById("next").style.display = "none";
        document.getElementById("doPredict").style.display = "inline";
        document.getElementById("resetBtn").style.display = "inline";

        //hide the result when next run is starting --> delete show class from result div
        document.getElementById("result").className = document.getElementById("result").className.replace("show", "");

        //check if its the last run
        if(selectedRepeats == currentRun){
            document.getElementById("next").innerHTML = "Übung abschließen";
        }
        
        //display the currentRun and the selectedRepeat for the user
        document.getElementById("repeatsDisp").innerHTML = currentRun+"/"+selectedRepeats;
        if(selectedRepeats>=currentRun){
            //this switch case can be removed and the logic of "zahlen" should be capeable for all cases
            if(checkLearningplan == "on"){
                console.log("learningplan !");
                let allValues = "<?php echo $dataset;?>";
                var data = allValues.split(",");
                var learningSelection = data[currentRun-1];
            }else{
                if(checkIntLearning == "off"){
                    console.log("default learning !");
                    let allValues = "<?php echo $dataset;?>";
                    var data = allValues.split(",");
                    var learningSelection = data[Math.floor(Math.random() * data.length)];
                }else{
                    console.log("Int learning !");
                    let weakValues = "<?php echo $convWeakValues;?>";
                    var data = weakValues.split(",");
                    var learningSelection = data[currentRun-1];
                }
            }
            document.getElementById("task").innerHTML = learningSelection;
            currentRun++;                    
        }else{
            //show results and hide buttons for checks
            document.getElementById("pieChartCanvas").style.display = "block";
            document.getElementById("endTraining").style.display = "block";
            document.getElementById("redoTraining").style.display = "block";
            document.getElementById("pieChartWrapper").style.display = "block";
            drawPieChart();

            document.getElementById("doPredict").style.display = "none";
            document.getElementById("resetBtn").style.display = "none";     

            //scroll to bottom if results are shown --> gets only used if the result is shown under the drawing element due to display width
            $('html,body').animate({scrollTop: document.body.scrollHeight},"slow");
        }
    }


    async function loadModel() {
        // clear the model variable
        model = undefined; 
        // load the model using a HTTPS request (where you have stored your model files)
        model = await tf.loadLayersModel("../saved_models/"+selectedCategory.toLowerCase()+"/model.json");
        /*
        switch (selectedCategory){
            case "Zahlen":
                model = await tf.loadLayersModel("../saved_models/zahlen/model.json")
                break;
            case "Buchstaben":
                model = await tf.loadLayersModel("../saved_models/buchstaben/model.json")
                break;
            case "Hiragana":
                model = await tf.loadLayersModel("../saved_models/hiragana/model.json")
                break;
            case "Formen":
                model = await tf.loadLayersModel("../saved_models/formen/model.json")
                break;
        }
        */
    }

    loadModel();


    function preprocessCanvas(image) {
        // resize the input image to target size of (1, 28, 28)
        let tensor = tf.browser.fromPixels(image)
            .resizeNearestNeighbor([28, 28])
            .mean(2)
            .expandDims(2)
            .expandDims()
            .toFloat();

        console.log(tensor.shape);

        return tensor.div(255.0);
    }


    $("#doPredict").click(async function () {
        // get image data from canvas
        /*
        const imageData = context.getImageData(
        0,
        0,
        canvas.width,
        canvas.height
        );
        */

        //attemp to downscale the canvas context by using another canvas
        /*
        const smallCanvas = document.getElementById("small");
        const smallContext = smallCanvas.getContext("2d");   
        smallContext.scale(0.25, 0.25);
        smallContext.drawImage(canvas, 0, 0); 
        const smallImageData = smallContext.getImageData(0, 0, smallCanvas.width, smallCanvas.height);
        */


        //rescaling
        //get only the drawn are and not the free space around
        var minX = Math.min.apply(Math, clickX) - 200;
        var maxX = Math.max.apply(Math, clickX) + 200;
        
        var minY = Math.min.apply(Math, clickY) - 200;
        var maxY = Math.max.apply(Math, clickY) + 200;

        var tempCanvas = document.createElement("canvas"),
        tCtx = tempCanvas.getContext("2d");

        tempCanvas.width  = maxX - minX;
        tempCanvas.height = maxY - minY;

        //tCtx.strokeStyle = "white";
        tCtx.drawImage(canvas, minX, minY, maxX - minX, maxY - minY, 0, 0, maxX - minX, maxY - minY);

        //check if the user has drawn smth. --> if not just return and give a error message to prevent js error
        if(tempCanvas.width == 0 && tempCanvas.height == 0){
            resultField.innerHTML = "Du hast noch nichts gezeichnet";
            showResultSnackbar();
            return;
        }
        const imageDataRescaled = tCtx.getImageData(
        0,
        0,
        tempCanvas.width,
        tempCanvas.height
        );


        //alternativly parse the canvas direct to function preprocessCanvas 
        let tensor = preprocessCanvas(imageDataRescaled);
        // preprocess canvas
        //let tensor = preprocessCanvas(imageData);
        // make predictions on the preprocessed image tensor
        let predictions = await model.predict(tensor).data();
        let output = model.predict(tensor).data();
        // get the model's prediction results
        let results = Array.from(predictions);
        //call the result method for the correct model (maybe can be done in one result function) 
    // let choosenCategory = document.getElementById("selectedCategory").value;
        switch (selectedCategory){
            case "Buchstaben":
                alphabetProcessResult(results);
                break;
            case "Zahlen":
                digitsProcessResult(results);
                break;
            case "Hiragana":
                hiraganaProcessResult(results);
                break;
            case "Formen":
                shapesProcessResult(results);
                break;
        }

        //show the next button again and hide the predict and reset btn till next is clicked (see categoryChooser())
        document.getElementById("next").style.display = "inline";
        document.getElementById("doPredict").style.display = "none";
        document.getElementById("resetBtn").style.display = "none";
        //document.getElementById("result").style.display = "block";
        showResultSnackbar();
    });


    function digitsProcessResult(r){
        let odd = Math.max(...r);
        let number = r.indexOf(odd);

        const numberDict = {
            "Null": 0,
            "Eins": 1,
            "Zwei": 2,
            "Drei": 3,
            "Vier": 4,
            "Fünf": 5,
            "Sechs": 6,
            "Sieben": 7,
            "Acht": 8,
            "Neun": 9
        }

        let disiredResult = numberDict[taskField.innerHTML];
        let answerResult = undefined;

        if(odd > threshold){
            if(disiredResult == number){
                resultField.innerHTML = "Richtig ! Sehr gut, Sie haben eine "+number+" gezeichnet. Die Übereinstimmung liegt bei: "+(odd*100).toFixed(2)+"%";
                answerResult = 1;
                totalRight++;
                //call function to save the drawn image - only if drawing is correct
                saveDrawnImage(selectedCategory, document.getElementById("task").innerHTML);
            }else{
                resultField.innerHTML = "Falsch, Sie haben zu "+(odd*100).toFixed(2)+"% eine "+number+" anstatt einer "+disiredResult+" gezeichnet.";
                answerResult = 0;
                totalWrong++;
            }
        }else{
            resultField.innerHTML = "Entschuldigung, deine Antwort konnte nicht erkannt werden. Du hast wahrscheinlich eine "+number+" gezeichnet. Die Übereinstimmung liegt bei: "+(odd*100).toFixed(2)+"%";
            totalUnknown++;
            return;
        }
        saveLearningResult(taskField.innerHTML, answerResult);
    }


    function alphabetProcessResult(r){
        //PROBLEM: lowercase and upper case cant be checked, because the model is handling upper and lower in the same class...
        let odd = Math.max(...r);
        let number = r.indexOf(odd);

        let alphabet = "<?php echo $dataset;?>";
        alphabet = alphabet.split(",");

        //-1 because the first one is a space   
        let drawnLetter = alphabet[number-1]
        let answerResult = undefined;
        let disiredResult = taskField.innerHTML;  
        if(odd > threshold){
            if(drawnLetter == disiredResult){
                resultField.innerHTML = "Richtig ! Sehr gut, Sie haben ein "+disiredResult+" gezeichnet. Die Übereinstimmung liegt bei: "+(odd*100).toFixed(2)+"%";
                answerResult = 1;
                totalRight++;
                //call function to save the drawn image - only if drawing is correct
                saveDrawnImage(selectedCategory, document.getElementById("task").innerHTML);
            }else{
                resultField.innerHTML = "Falsch, Sie haben zu "+(odd*100).toFixed(2)+"% eine "+drawnLetter+" anstatt einer "+disiredResult+" gezeichnet.";
                answerResult = 0;
                totalWrong++;
            }
        }else{
            resultField.innerHTML = "Entschuldigung, deine Antwort konnte nicht erkannt werden. Du hast wahrscheinlich ein "+drawnLetter+" gezeichnet. Die Übereinstimmung liegt bei: "+(odd*100).toFixed(2)+"%";
            totalUnknown++;
            return;
        }
        saveLearningResult(taskField.innerHTML, answerResult);
    }


    function hiraganaProcessResult(r){

        let odd = Math.max(...r);
        let number = r.indexOf(odd);

        let charDict= {
            "O": 0,
            "Ki": 1,
            "Su": 2,
            "Tsu": 3,
            "Na": 4,
            "Ha": 5,
            "Ma": 6,
            "Ya": 7,
            "Re": 8,
            "Wo": 9
        }

        let numberToJap= {
            0: "お",
            1: "き",
            2: "す",
            3: "つ",
            4: "な",
            5: "は",
            6: "ま",
            7: "や",
            8: "れ",
            9: "を"
        }
        let disiredResult = charDict[taskField.innerHTML];
        let drawnChar = Object.keys(charDict).find(k=>charDict[k]===number);
        let key = Object.keys(charDict).find(k=>charDict[k]===disiredResult);
        let japChar = numberToJap[disiredResult];
        let drawnJapChar = numberToJap[number];

        let answerResult = undefined;
        if(odd > threshold){
            if(disiredResult == number){
                resultField.innerHTML = "Richtig ! Sehr gut, Sie haben ein "+key+" ("+japChar+") gezeichnet. Die Übereinstimmung liegt bei: "+(odd*100).toFixed(2)+"%";
                answerResult = 1;
                totalRight++;
                //call function to save the drawn image - only if drawing is correct
                saveDrawnImage(selectedCategory, document.getElementById("task").innerHTML);
            }else{
                resultField.innerHTML = "Falsch, Sie haben zu "+(odd*100).toFixed(2)+"% ein "+drawnChar+" ("+drawnJapChar+") anstatt des "+taskField.innerHTML+" ("+japChar+") gezeichnet.";
                answerResult = 0;
                totalWrong++;
            }
        }else{
            resultField.innerHTML = "Entschuldigung, deine Antwort konnte nicht erkannt werden. Du hast wahrscheinlich ein "+key+" gezeichnet. Die Übereinstimmung liegt bei: "+(odd*100).toFixed(2)+"%";
            totalUnknown++;
            return;
        }
        saveLearningResult(taskField.innerHTML, answerResult);
    }


    function shapesProcessResult(r){

        let odd = Math.max(...r);
        let number = r.indexOf(odd);

        let shapes = "<?php echo $dataset;?>";
        shapes = shapes.split(",");

        let disiredResult = taskField.innerHTML;
        let predictedShape = shapes[number];
        if(odd > threshold){
            if(disiredResult == predictedShape){
                resultField.innerHTML = "Richtig ! Sehr gut, Sie haben ein "+predictedShape+" gezeichnet. Die Übereinstimmung liegt bei: "+(odd*100).toFixed(2)+"%";
                answerResult = 1;
                totalRight++;
                //call function to save the drawn image - only if drawing is correct
                saveDrawnImage(selectedCategory, document.getElementById("task").innerHTML);
            }else{
                resultField.innerHTML = "Falsch, Sie haben zu "+(odd*100).toFixed(2)+"% ein "+predictedShape+" anstatt eines "+disiredResult+" gezeichnet.";
                answerResult = 0;
                totalWrong++;
            }
        }else{
            resultField.innerHTML = "Entschuldigung, deine Antwort konnte nicht erkannt werden. Du hast wahrscheinlich ein "+predictedShape+" gezeichnet. Die Übereinstimmung liegt bei: "+(odd*100).toFixed(2)+"%";
            totalUnknown++;
            return;
        }
        saveLearningResult(taskField.innerHTML, answerResult);
    }


    function saveLearningResult(data,result){
        $.ajax({
            type: "POST",
            url: "backend.php",
            data: {
                data: data,
                method: "learningResults",
                result: result,
                category: selectedCategory
            },
            success: function(result, message, response) {
                console.log(result);
                console.log(message);
                console.log(response);
            }
        });
    }   


    function saveDrawnImage(cat, label){
        var dataURL = canvas.toDataURL();
        $.ajax({
            type: "POST",
            url: "backend.php",
            data: {
                category: cat,
                label: label,
                method: "saveDrawing",
                imgBase64: dataURL,
                date: new Date().toDateString()
            },
            success: function(result, message, response) {
                console.log(result);
                console.log(message);
                console.log(response);
            }
        });
    }


    function drawPieChart(){
        var xValues = ["Richtige Antworten", "Falsche Antworten","Ungewertete Antworten"];
        var yValues = [totalRight, totalWrong, totalUnknown];
        var barColors = [
        "#109d1b",
        "#cb3c3c",
        "#cda71f"
        ];

        new Chart("pieChartCanvas", {
        type: "pie",
        data: {
            labels: xValues,
            datasets: [{
            backgroundColor: barColors,
            data: yValues
            }]
        },
        options: {
            title: {
                display: true,
                text: "Übersicht Ihrer Ergebnisse",
                fontSize: 18
            }
        }
        });
    }

    function loadVoices(){
        //voices needs to get loaded before the function textToSpeech is executed, otherwise its not loading fast enough
        let voices = window.speechSynthesis.getVoices();
        return voices;
    }

    function textToSpeech(){
        let fetchedText = document.getElementById("task").innerHTML;
        let category = document.getElementById("selectedCategory").value;
        let msg = new SpeechSynthesisUtterance();
        let voices = loadVoices();
        let textVal;
        switch (category){
        case "Formen":

            break;
        case "Buchstaben":
            textVal = "Zeichnen Sie den Buchstaben "+fetchedText;
            break;
        case "Zahlen":
            textVal = "Zeichnen Sie die Zahl "+fetchedText;
            break;
        case "Hiragana":
            textVal = "Zeichnen Sie das Hiragana Symbol für "+fetchedText;
            break;
        }
        msg.voice = voices[2]; //0-3 possible for german
        msg.volume = 1; // From 0 to 1
        msg.rate = 0.7; // From 0.1 to 10
        msg.pitch = 1; // From 0 to 2
        msg.text = textVal;
        msg.lang = 'de';
        speechSynthesis.speak(msg);
    }

    function showResultSnackbar() {
        var resultDiv = document.getElementById("result");
        if(resultDiv.innerHTML.includes("Richtig")){
            resultDiv.style.backgroundColor ="#49873a";
        }else if(resultDiv.innerHTML.includes("Falsch")){
            resultDiv.style.backgroundColor ="#e36565";
        }else{
            resultDiv.style.backgroundColor ="#cda71f";
        }
        resultDiv.className = "show";
        //setTimeout(function(){ resultDiv.className = resultDiv.className.replace("show", ""); }, 10000);
    }
</script>

<?php
}else{
    header("LOCATION: index.html");
}
?>