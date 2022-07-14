<?php
include 'db_connector.php';
session_start();
if(isset($_SESSION["idUser"])){

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

    if(isset($_POST["intLearning"])){
        $inteligentLearning = "on";
    }else{
        $inteligentLearning = "off";
    }

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
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles/drawingStyle.css">
    <link rel="stylesheet" href="styles/background.css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <!--<script src="externScripts/tf.min.js"></script>-->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@2.0.0/dist/tf.min.js"></script>
</head>
<body class="area" onload="categoryChooser()">
    <h1 id="headline"><?= $_POST["category"]?> lernen</h1>
    <div id="contentWrapper">
        <div id="wrapper1">
            <div id="infoDisplay">
                <p>Kategorie: <?= $_POST["category"]?></p>
                <p>Anzahl der Übungen: <?= $_POST["repeats"]?></p>
                <p>Intelligentes lernen: <?= $inteligentLearning?></p>
                <p id="displayCurrentRepeat">Durchlauf:<span id="repeatsDisp"></span></p>
            </div>
            <input type="hidden" id="selectedCategory" value="<?= $_POST["category"]?>">
            <input type="hidden" id="selectedRepeats" value="<?= $_POST["repeats"]?>">
            <input type="hidden" id="uuid" value="<?= $_SESSION["uuid"]?>">
            <input type="hidden" id="intLearning" value="<?= $inteligentLearning ?>">
            <div id="taskWrapper">
                <p>Zeichnen Sie eine: <img src="media/speaker.svg" width="20px" height="20px" id="playAudio" onclick="textToSpeech()"></p> 
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
            <a href="settings.php" id="endTraining" style="display:none;">Übung Beenden</a>
            <a href="#" id="redoTraining" style="display:none;" onclick="location.reload()">Übung Wiederholen</a>
        </div>
    </div>
    <div id="result"></div>

    <!--downscale attempt-->
    <!--<canvas id="small" width="150px" height="150px" style="background-color:black; color: white;"></canvas> -->

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

// clear the canvas and the categorie selection
$("#next").click(async function () {
    context.clearRect(0, 0, canvasWidth, canvasHeight);
    clickX = new Array();
    clickY = new Array();
    clickD = new Array();
    //document.getElementById("result").innerHTML = "";
    //document.getElementById("task").innerHTML = "";
});

//---------------------------------------------------------//
//               End of Canvas Drawing                     //
//---------------------------------------------------------//



var totalRight = 0;
var totalWrong = 0;

function categoryChooser(){
    let selectedCategory = document.getElementById("selectedCategory").value;
    let selectedRepeats = parseInt(document.getElementById("selectedRepeats").value);
    let checkIntLearning = document.getElementById("intLearning").value;

    //hide the next button again till the next predict is done
    document.getElementById("next").style.display = "none";

    //check if its the last run
    if(selectedRepeats == currentRun){
        document.getElementById("next").innerHTML = "Übung abschließen";
    }
    
    //display the currentRun and the selectedRepeat for the user
    document.getElementById("repeatsDisp").innerHTML = currentRun+"/"+selectedRepeats;
    if(selectedRepeats>=currentRun){
        console.log("true");
        switch (selectedCategory){
            case "Formen":
                break;
            case "Buchstaben":
                break;
            case "Zahlen":
                if(checkIntLearning == "off"){
                    let allValues = "<?php echo $dataset;?>";
                    var data = allValues.split(",");
                    var learningSelection = data[Math.floor(Math.random() * data.length)];
                }else{
                    let weakValues = "<?php echo $convWeakValues;?>";
                    var data = weakValues.split(",");
                    var learningSelection = data[currentRun-1];
                    console.log(data);
                    console.log(learningSelection);
                }
                document.getElementById("task").innerHTML = learningSelection;
                break;
            case "Japanisch":
                break;
        }
        currentRun++;
    }else{
        //alert placeholder --> maybe add some stats showcase and call the setting page later with another button
        //alert("Übung abgeschlossen sehr gut !");
        document.getElementById("pieChartCanvas").style.display = "block";
        document.getElementById("endTraining").style.display = "block";
        document.getElementById("redoTraining").style.display = "block";
        document.getElementById("pieChartWrapper").style.display = "block";
        drawPieChart();
        //window.location.href = "settings.php"
        
    }
}

async function loadModel() {
    // clear the model variable
    model = undefined; 
    // load the model using a HTTPS request (where you have stored your model files)
    model = await tf.loadLayersModel("../saved_models/model.json");
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
   // tempCanvas.style.backgroundColor = "black";
    tempCanvas.width  = maxX - minX;
    tempCanvas.height = maxY - minY;
    console.log(tempCanvas.width);
    console.log(tempCanvas.height);
   // tCtx.strokeStyle = "white";
    tCtx.drawImage(canvas, minX, minY, maxX - minX, maxY - minY, 0, 0, maxX - minX, maxY - minY);

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
    let choosenCategory = document.getElementById("selectedCategory").value;
    switch (choosenCategory){
    case "Formen":
        break;
    case "Buchstaben":
        break;
    case "Zahlen":
        digitsProcessResult(results);
        break;
    case "Japanisch":
        break;
    }
    //call function to save the drawn image
    saveDrawnImage(choosenCategory);
    document.getElementById("next").style.display = "inline";
});

function digitsProcessResult(r){

    let odd = Math.max(...r);
    let number = r.indexOf(odd);

    let resultField = document.getElementById("result");
    let taskField = document.getElementById("task");

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

    let drawnNumber = numberDict[taskField.innerHTML];
    let answerResult = undefined;
    if(drawnNumber == number){
        resultField.innerHTML = "Richtig ! Sehr gut, Sie haben eine "+number+" gezeichnet. <br> Die Übereinstimmung liegt bei: "+(odd*100).toFixed(2)+"%";
        answerResult = 1;
        totalRight++;
    }else{
        resultField.innerHTML = "Falsch, Sie haben zu "+(odd*100).toFixed(2)+"% eine "+number+" anstatt einer "+drawnNumber+" gezeichnet.";
        answerResult = 0;
        totalWrong++;
    }
    var foo = "Test";
    saveLearningResult(taskField.innerHTML, answerResult);
}

function saveLearningResult(data,result){
    let category = document.getElementById("selectedCategory").value;
    let uuid = document.getElementById("uuid").value;

    $.ajax({
        type: "POST",
        url: "backend.php",
        data: {
            data: data,
            method: "learningResults",
            result: result,
            category: category,
            uuid: uuid
        },
        success: function(result, message, response) {
			console.log(result);
            console.log(message);
            console.log(response);
		}
	});
}   


function saveDrawnImage(cat){
    var dataURL = canvas.toDataURL();
    $.ajax({
        type: "POST",
        url: "backend.php",
        data: {
            category: cat,
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
    var xValues = ["Richtige Antworten", "Falsche Antworten",];
    var yValues = [totalRight, totalWrong];
    var barColors = [
    "#109d1b",
    "#cb3c3c"
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

function textToSpeech(){
    let fetchedText = document.getElementById("task").innerHTML;
    console.log(fetchedText);
    let msg = new SpeechSynthesisUtterance();
    //let voices = window.speechSynthesis.getVoices();
    //msg.voice = voices[8]; 
    msg.volume = 1; // From 0 to 1
    msg.rate = 0.5; // From 0.1 to 10
    msg.pitch = 1; // From 0 to 2
    msg.text = "Zeichnen Sie eine "+fetchedText;
    msg.lang = 'de';
    speechSynthesis.speak(msg);
}
</script>

<?php
}else{
    echo "Access denied";
}
?>