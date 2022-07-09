<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="tf.min.js"></script>
</head>
<body>
    <div>
        <p>Kategorie: <?= $_POST["category"]?></p>
        <p>Anzahl der Übungen: <?= $_POST["repeats"]?></p>
    </div>
    <div>
        <p>Kategorie wählen</p>
        <select id="categorie" onchange="randomChooser(this)">
            <option>---</option>
            <option value="digits">Zahlen</option>
            <option value="words">Wörter</option>
        </select>
    </div>
    <div id="task"></div>
    <div id="canvasWrapper"></div>
    <button id="doPredict">Predict</button>
    <button id="resetBtn">Reset</button>
    <div id="result"></div>
</body>
</html>
<script>
 
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
var categorie = document.getElementById("categorie");


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
    }else{
        categorie.style.border = "2px solid red";
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
$("#resetBtn").click(async function () {
    context.clearRect(0, 0, canvasWidth, canvasHeight);
    clickX = new Array();
    clickY = new Array();
    clickD = new Array();
    categorie.selectedIndex = 0;
    document.getElementById("result").innerHTML = "";
    document.getElementById("task").innerHTML = "";
});

//---------------------------------------------------------//
//               End of Canvas Drawing                     //
//---------------------------------------------------------//




function randomChooser(e){
    let chosenLevel = e.value;
    e.style.border = "none";
    if(chosenLevel == "digits"){
        const digits = "9";
        var rndSel = digits[Math.floor(Math.random() * digits.length)];
    }else{
        const words = ["foo","bar","buz"];
        var rndSel = words[Math.floor(Math.random()*words.length)];
    }
    document.getElementById("task").innerHTML = rndSel;
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
    console.log(imageData)
    console.log(canvas)
    //alternativly parse the canvas direct to function preprocessCanvas (works a bit worse somehow)
    // preprocess canvas
    let tensor = preprocessCanvas(imageData);
    // make predictions on the preprocessed image tensor
    let predictions = await model.predict(tensor).data();
    let output = model.predict(tensor).data();
    // get the model's prediction results
    let results = Array.from(predictions);
    if(categorie.value == "digits"){
        digitsProcessResult(results);
    }
    
});

function digitsProcessResult(r){
    console.log(r);
    let max = Math.max(...r);
    console.log(max);
    let maxIndex = r.indexOf(max);
    console.log(maxIndex);

    let resultField = document.getElementById("result");
    let taskField = document.getElementById("task");
    if(taskField.innerHTML == maxIndex){
        resultField.innerHTML = "Richtig ! Sehr gut, Sie haben eine "+maxIndex+" gezeichnet. <br> Die Übereinstimmung liegt bei: "+(max*100).toFixed(2)+"%";
    }else{
        resultField.innerHTML = "Falsch, Sie haben zu "+(max*100).toFixed(2)+"% eine "+maxIndex+" anstatt einer "+taskField.innerHTML+" gezeichnet.";
    }
}
</script>