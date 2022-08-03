# Learning By Drawing
Das Learning-By-Drawing Programm ist eine Webanwendung die für das lernen verschiedener Kategorien verwendet werden kann. Dabei lernen die Nutzer die Inhalte indem sie diese Zeichenen. Der Fokus dieses Lernprogrammes liegt dabei auch Buchstaben, Zahlen, Formen und Zeichen. Zu überprüfung der Anntworten werden Neuronale Nezte verwendet die die geziechneten Bilder überprüfen.

***

## Projekt Struktur

Die Anwendungsdateien liegen in dem Ordner **"project"**. 
Im Ordner **"learningData"** sind verschiedene Daten zum Training der Modelle abgelegt. Einige Daten wurden auch aus online Bibliotheken bezogen und befinden sich nicht im Ordner. 
Der Ordner **"logs"** enthält die Ergbenisse bzw. Log-Dateien der Machine-Learning Modell Trainingsdurchgänge.
Die gespeicherten Modelle sind im Ordner **"saved_models"** gespeichert und dort auch in ihre Kategorien unterteilt.
Der Ordner **"templates"** beinhaltet alle Inhalte der Weboberfläche.
Die Python-Dateien der Modelle befinden sich im project Ordner. 
In dem Ordner **"documentation"** können alle Dokumente die zu diesem Projekt erstellt wurden gefunden werden.

***

## Installation 

1. Für die Verwendung der Anwendung wird ein Webserver (z.B. Apache) mit einer MySQL Datenbank benötigt.

2. Die MySQL-Datei "learningbydrawing.sql" muss importiert werden.

3. Der Ordner "templates" und der Ordner "saved_models" müssen auf den Webserver geladen werden.

4. Die Anwendung kann nun unter entsprechender Adresse aufgerufen und verwendet werden.
