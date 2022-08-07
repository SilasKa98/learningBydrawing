# Learning By Drawing
Das Learning-By-Drawing Programm ist eine Webanwendung die für das Lernen verschiedener Kategorien verwendet werden kann. Dabei lernen die Nutzer die Inhalte indem sie diese Zeichnen. Der Fokus dieses Lernprogrammes liegt dabei auf Buchstaben, Zahlen, Formen und Zeichen. Zur Überprüfung der Antworten werden Neuronale Netze verwendet.

***

## Projekt Struktur

Die Anwendungsdateien liegen in dem Ordner **"project"**. 
Im Ordner **"learningData"** sind verschiedene Daten zum Training der Modelle abgelegt. Einige Daten wurden auch aus online Bibliotheken bezogen und befinden sich nicht im Ordner. 
Der Ordner **"logs"** enthält die Ergbenisse bzw. Log-Dateien der Machine-Learning Modell Trainingsdurchgänge.
Die gespeicherten Modelle sind im Ordner **"saved_models"** gespeichert und dort auch in ihre Kategorien unterteilt.
Der Ordner **"templates"** beinhaltet alle Inhalte der Weboberfläche.
Die Python-Dateien der Modelle befinden sich im project Ordner. 
In dem Ordner **"documentation"** können alle Dokumente die zu diesem Projekt erstellt wurden gefunden werden.

## Installation 

1. Für die Verwendung der Anwendung wird ein Webserver (z.B. Apache) mit einer MySQL Datenbank benötigt.

2. Die MySQL-Datei "learningbydrawing.sql" muss importiert werden.

3. Der Ordner "templates" und der Ordner "saved_models" müssen auf den Webserver geladen werden.

4. Die Anwendung kann nun unter entsprechender Adresse aufgerufen und verwendet werden.

## Datenquellen

In diesem Teil werden die Quellen der Daten verlinkt die für das Training der neuronalen Netze verwendet wurden.
- **"learningAlphabet4.py:"** https://www.tensorflow.org/datasets/catalog/emnist (last access: 07.08.2022)
- **"learningDigitsOwnNumbers.py:"** https://www.tensorflow.org/datasets/catalog/mnist (last access: 07.08.2022)
- **"learningDigitsOwnNumbers2.py:"** https://www.tensorflow.org/datasets/catalog/mnist (last access: 07.08.2022)
- **"learningKuzushiji.py:"** https://www.tensorflow.org/datasets/catalog/kmnist (last access: 07.08.2022) und https://www.kaggle.com/datasets/anokas/kuzushiji?resource=download&select=k49_classmap.csv (last access: 07.08.2022)
- **"learningShapes.py:"** https://www.kaggle.com/code/stpeteishii/geometric-shapes-classify-densenet201/data (last access: 07.08.2022)