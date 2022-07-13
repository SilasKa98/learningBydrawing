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
    echo "all: ".$convWeakValues;
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles/styles.css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="externScripts/tf.min.js"></script>
</head>
<body onload="categoryChooser()">
    <div>
        <p>Kategorie: <?= $_POST["category"]?></p>
        <p>Anzahl der Übungen: <?= $_POST["repeats"]?></p>
        <p>Intelligentes lernen: <?= $inteligentLearning?></p>
        <p id="displayCurrentRepeat">Durchlauf:<span id="repeatsDisp"></span></p>
    </div>
    <input type="hidden" id="selectedCategory" value="<?= $_POST["category"]?>">
    <input type="hidden" id="selectedRepeats" value="<?= $_POST["repeats"]?>">
    <input type="hidden" id="uuid" value="<?= $_SESSION["uuid"]?>">
    <input type="hidden" id="intLearning" value="<?= $inteligentLearning ?>">
    Zeichnen Sie eine: <div id="task"></div>
    <img src="media/speaker.svg" width="50px" height="50px" id="playAudio" onclick="textToSpeech()">
    <div id="canvasWrapper"></div>
    <button id="doPredict">Predict</button>
    <button id="resetBtn">Reset</button>
    <button id="next" onclick="categoryChooser()" style="display:none;">next</button>
    <div id="result"></div>
</body>
</html>
<script>

var currentRun = 1;
var canvasWidth = 150;
var canvasHeight = 150;
var canvasStrokeStyle = "white";
var canvasLineJoin = "round";
var canvasLineWidth = 10;
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
canvasBox.appendChild(canvas);
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




function categoryChooser(){
    let selectedCategory = document.getElementById("selectedCategory").value;
    let selectedRepeats = parseInt(document.getElementById("selectedRepeats").value);
    let checkIntLearning = document.getElementById("intLearning").value;

    //hide the next button again till the next predict is done
    document.getElementById("next").style.display = "none";
    
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
                }
                document.getElementById("task").innerHTML = learningSelection;
                break;
            case "Japanisch":
                break;
        }
        currentRun++;
    }else{
        //alert placeholder --> maybe add some stats showcase and call the setting page later with another button
        alert("Übung abgeschlossen sehr gut !");
        window.location.href = "settings.php"
        
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
    return tensor.div(255.0);
}

$("#doPredict").click(async function () {
    // get image data from canvas
    const imageData = context.getImageData(
      0,
      0,
      canvas.width,
      canvas.height
    );

    //alternativly parse the canvas direct to function preprocessCanvas (works a bit worse somehow)
    // preprocess canvas
    let tensor = preprocessCanvas(imageData);
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
    }else{
        resultField.innerHTML = "Falsch, Sie haben zu "+(odd*100).toFixed(2)+"% eine "+number+" anstatt einer "+drawnNumber+" gezeichnet.";
        answerResult = 0;
    }
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